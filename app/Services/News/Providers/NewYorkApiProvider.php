<?php

namespace App\Services\News\Providers;

use App\Models\Articles\Articles;
use App\Services\MainService;
use App\Services\News\Contracts\NewsApiInterface;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class NewYorkApiProvider.
 */
class NewYorkApiProvider extends MainService implements NewsApiInterface
{

    public function __construct()
    {
        $this->baseUrl = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
        $this->apiKey = config('services.new_york_api.key');
    }


    public function fetchArticles(array $filters): array
    {
        try {

            if (!isset($filters['is_command']) || !$filters['is_command']) {

                $articles = Articles::filterArticles($filters)->get();


                if ($articles->isNotEmpty()) {
                    return $articles->toArray();
                }
            }

            $params = [
                'api-key' => $this->apiKey,
                'q' => $filters['q'] ?? null,
                'begin_date' => isset($filters['from']) ? Carbon::parse($filters['from'])->format('Ymd') : null,
                'end_date' => isset($filters['to']) ? Carbon::parse($filters['to'])->format('Ymd') : null,
            ];

            if (!empty($filters['category'])) {
                $category = ucfirst($filters['category']);

                if (!empty($params['q'])) {
                    $params['q'] = '(' . $params['q'] . ') AND (section_name:"' . $category . '" OR news_desk:"' . $category . '")';
                } else {
                    $params['q'] = 'section_name:"' . $category . '" OR news_desk:"' . $category . '"';
                }
            }

            $response = Http::withOptions([
                'verify' => checkIsLocalMode() ? false : true
            ])->get($this->baseUrl, $params);

            if ($response->successful()) {

                $docs = $response->json()['response']['docs'] ?? [];

                $articles = collect($docs)->map(function ($item) use ($filters) {
                    return [
                        'title' => $item['headline']['main'] ?? null,
                        'source' => $item['source'] ?? 'The New York Times',
                        'url' => $item['web_url'] ?? null,
                        'published_at' => isset($item['pub_date']) ? Carbon::parse($item['pub_date'])->toDateString() : null,
                        'author' => $item['byline']['original'] ?? 'The New York Times',
                        'description' => $item['abstract'] ?? null,
                        'content' => $item['lead_paragraph'] ?? $item['snippet'] ?? null,
                        'category' => $filters['category'] ?? null,
                    ];
                });

                if (!empty($filters['author'])) {
                    $articles = $articles->filter(function ($article) use ($filters) {
                        return !empty($article['author']) &&
                            stripos($article['author'], $filters['author']) !== false;
                    });
                }

                return $articles->values()->toArray();
            }

            Log::error("New York API error", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => $response->status(),
                'body' => $response->body(),
            ];

        } catch (\Exception $exception) {

            Log::error('NewYorkApiProvider fetchArticles error', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

    }

}
