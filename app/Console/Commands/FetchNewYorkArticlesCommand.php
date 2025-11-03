<?php

namespace App\Console\Commands;

use App\Enums\SourceTypes;
use App\Models\Articles\Articles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchNewYorkArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    //TODO: example for calling in the cli : php artisan news:fetch-new-york --q="ai" --from=2024-01-01 --to=2024-09-01 --category=science --author="By Steve Lohr and Spencer Lowell"
    protected $signature = 'news:fetch-new-york
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
    protected $description = 'Fetch latest articles from The New York Times API and store them locally.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            $this->info('Fetching articles from The New York Times...');

            $filters = [
                'q' => $this->option('q') ?? 'technology',
                'from' => $this->option('from') ?? null,
                'to' => $this->option('to') ?? null,
                'category' => $this->option('category') ?? null,
                'author' => $this->option('author') ?? null,
                'is_command' => true,
            ];

            $type = 'new_york';
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
                        'source' => $item['source'] ?? null,
                        'title' => $item['title'] ?? null,
                        'author' => $item['author'] ?? null,
                        'description' => $item['description'] ?? null,
                        'content' => $item['content'] ?? null,
                        'published_at' => $item['published_at'] ?? null,
                        'category' => $item['category'] ?? null,
                        'type' => SourceTypes::NEW_YORK_API,
                        'order' => ++$index,
                    ]
                );
            }

            DB::commit();

            $this->info('Articles from The New York Times successfully stored in database.');

        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error('FetchNewYorkArticlesCommand error', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

    }
}
