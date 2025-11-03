<?php

namespace App\Services\News\Providers;

use App\Models\Articles\Articles;
use App\Services\MainService;
use App\Services\News\Contracts\NewsApiInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


/**
 * Class NewsApiProvider.
 */
class NewsApiProvider extends MainService implements NewsApiInterface
{

    public function __construct()
    {
        $this->baseUrl = 'https://newsapi.org/v2/';
        $this->apiKey = config('services.news_api.key');
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

            $endpoint = empty($filters['category']) ? 'everything' : 'top-headlines';

            $params = [
                'apiKey' => $this->apiKey,
                'q' => $filters['q'] ?? 'technology',
                'from' => $filters['from'] ?? null,
                'to' => $filters['to'] ?? null,
                'sortBy' => $filters['sortBy'] ?? 'publishedAt',
                'language' => 'en',
                'pageSize' => $this->apiPaginate ?? 20,
            ];

            if (!empty($filters['category'])) {
                $params['category'] = $filters['category'];
            }

            $response = Http::withOptions([
                'verify' => checkIsLocalMode() ? false : true
            ])->get($this->baseUrl . $endpoint, $params);


            if ($response->successful()) {

                $data = $response->json();

                if (isset($data['articles']) && is_array($data['articles'])) {

                    $articles = collect($data['articles'])->map(function ($item) use ($filters) {

                        return [
                            'title' => $item['title'] ?? null,
                            'source' => $item['source']['name'] ?? 'NewsAPI',
                            'url' => $item['url'] ?? null,
                            'published_at' => isset($item['publishedAt'])
                                ? Carbon::parse($item['publishedAt'])->toDateString()
                                : null,
                            'author' => $item['author'] ?? null,
                            'description' => $item['description'] ?? null,
                            'content' => $item['content'] ?? null,
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

            }

            Log::error("NewsAPI error", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];

        } catch (\Exception $exception) {

            Log::error('NewsApiProvider fetchArticles error', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return [];
        }

    }


}
