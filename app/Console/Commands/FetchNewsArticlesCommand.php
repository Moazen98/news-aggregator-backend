<?php

namespace App\Console\Commands;

use App\Enums\SourceTypes;
use App\Models\Articles\Articles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchNewsArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //TODO: example for calling in the cli : php artisan news:fetch-news --q="ai" --from=2025-10-10
    protected $signature = 'news:fetch-news
                        {--q= : Search keyword}
                        {--from= : Start date (YYYY-MM-DD)}
                        {--to= : End date (YYYY-MM-DD)}
                        {--category= : Category (technology - science - world)}
                        {--author= : Author name (Andrew)}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest news articles from NewsAPI';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        try {

        $filters = [
            'q' => $this->option('q') ?? 'technology',
            'from' => $this->option('from') ?? null,
            'to' => $this->option('to') ?? null,
            'category' => $this->option('category') ?? null,
            'author' => $this->option('author') ?? null,
            'is_command' => true,
        ];

        $type = 'news_api';
        $factory = app('servicesV1');
        $provider = $factory::make($type);

        $articles = $provider->fetchArticles($filters);

        if (empty($articles)) {
            $this->error("No articles were fetched. Please check the API key or filters,You are trying to request results too far in the past or set the query search.");
            return;
        }

        DB::beginTransaction();

        foreach ($articles as $index => $item) {
            Articles::updateOrCreate(
                ['url' => $item['url']],
                [
                    'source'       => $item['source'] ?? null,
                    'title'        => $item['title'] ?? null,
                    'author'       => $item['author'] ?? null,
                    'description'  => $item['description'] ?? null,
                    'content'      => $item['content'] ?? null,
                    'published_at' => $item['published_at'] ?? null,
                    'category' => $item['category'] ?? null,
                    'type'         => SourceTypes::NEWS_API,
                    'order'        => $index + 1,
                ]
            );
        }

        DB::commit();

        $this->info('News fetched and stored successfully.');

        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error('FetchNewsArticlesCommand error', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

    }
}
