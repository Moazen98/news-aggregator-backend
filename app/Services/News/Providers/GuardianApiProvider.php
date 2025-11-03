<?php

namespace App\Services\News\Providers;

use App\Models\Articles\Articles;
use App\Services\MainService;
use App\Services\News\Contracts\NewsApiInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Class GuardianApiProvider.
 */
class GuardianApiProvider extends MainService implements NewsApiInterface
{


    public function __construct()
    {
        $this->baseUrl = 'https://content.guardianapis.com/search';
        $this->apiKey = config('services.guardian_api.key');
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
                'api-key' => $this->apiKey,
                'q' => $filters['q'] ?? null,
                'from-date' => $filters['from'] ?? null,
                'to-date' => $filters['to'] ?? null,
                'section' => $filters['category'] ?? null,
                'show-fields' => 'byline,trailText,bodyText',
            ];

            if (isset($filters['author']) && !empty($filters['author'])) {
                $query['q'] = trim(($query['q'] ?? '') . ' "' . $filters['author'] . '"');
            }


            $response = Http::withOptions([
                'verify' => checkIsLocalMode() ? false : true
            ])->get($this->baseUrl, $query);


            if ($response->successful()) {
                $results = $response->json()['response']['results'] ?? [];

                return collect($results)->map(function ($item) {
                    return [
                        'title' => $item['webTitle'] ?? null,
                        'source' => 'The Guardian',
                        'url' => $item['webUrl'] ?? null,
                        'published_at' => isset($item['webPublicationDate'])
                            ? Carbon::parse($item['webPublicationDate'])->toDateString()
                            : null,
                        'author' => $item['fields']['byline'] ?? 'The Guardian',
                        'description' => $item['fields']['trailText'] ?? null,
                        'content' => $item['fields']['bodyText'] ?? null,
                        'category' => $item['sectionName'] ?? null,
                    ];
                })->toArray();
            }

            Log::error("Guardian API error", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];

        } catch (\Exception $exception) {

            Log::error('GuardianApiProvider fetchArticles error', [
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
