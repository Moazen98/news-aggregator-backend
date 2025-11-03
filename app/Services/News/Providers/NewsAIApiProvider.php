<?php

namespace App\Services\News\Providers;

use App\Models\Articles\Articles;
use App\Services\MainService;
use App\Services\News\Contracts\NewsApiInterface;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;


/**
 * Class NewsApiProvider.
 */
class NewsAIApiProvider extends MainService implements NewsApiInterface
{

    public function __construct()
    {
        $this->baseUrl = 'https://newsapi.ai/api/v1/article/getArticles';
        $this->apiKey = config('services.news_api_ai.key');
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

            $query = [
                '$query' => []
            ];

            $query['$query']['keyword'] = $filters['q'] ?? 'technology';

            if (!empty($filters['from'])) {
                $query['$query']['dateStart'] = Carbon::parse($filters['from'])->toDateString();
            }

            if (!empty($filters['to'])) {
                $query['$query']['dateEnd'] = Carbon::parse($filters['to'])->toDateString();
            }

            if (!empty($filters['category'])) {
                $query['$query']['category'] = $filters['category'];
            }

            if (empty($query['$query'])) {
                return [];
            }


            $response = Http::withOptions([
                'verify' => checkIsLocalMode() ? false : true
            ])->post($this->baseUrl, [
                'apiKey' => $this->apiKey,
                'query' => $query,
                'language' => 'eng',
            ]);

            if ($response->successful()) {
                $results = $response->json()['articles']["results"] ?? [];


                $articles = collect($results)->map(function ($item) use ($filters) {

                    return [
                        'title' => $item['title'] ?? null,
                        'source' => $item['source']['title'] ?? 'NewsAPI AI',
                        'url' => $item['url'] ?? null,
                        'published_at' => isset($item['dateTime'])
                            ? Carbon::parse($item['dateTime'])->toDateString()
                            : null,
                        'author' => (isset($item['authors']) && count($item['authors'])) > 0 ? $item['authors'][0]['name'] : 'NewsAPI AI',
                        'description' => $item['body'] ?? null,
                        'content' => $item['body'] ?? null,
                        'category' => $item['category'] ?? ($filters['category'] ?? null),
                    ];
                });

                if (!empty($filters['author'])) {
                    $articles = $articles->filter(function ($article) use ($filters) {
                        return !empty($article['author']) && stripos($article['author'], $filters['author']) !== false;
                    });
                }

                return $articles->values()->toArray();
            }

            Log::error("NewsApi API error", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];

        } catch (\Exception $exception) {

            Log::error('NewsAIApiProvider fetchArticles error', [
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

