<?php

namespace App\Console\Commands;

use App\Enums\SourceTypes;
use App\Models\Articles\Articles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchGuardianArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    //TODO: example for calling in the cli : php artisan news:fetch-guardian --q="global warming" --from="2025-10-01" --to="2025-10-28" --category="science"
    protected $signature = 'news:fetch-guardian
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
    protected $description = 'Fetch latest articles from The Guardian API and store them locally.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            $this->info('Fetching articles from The Guardian...');

            $filters = [
                'q' => $this->option('q') ?? 'technology',
                'from' => $this->option('from') ?? null,
                'to' => $this->option('to') ?? null,
                'section' => $this->option('category') ?? null,
                'author' => $this->option('author') ?? null,
                'is_command' => true,
            ];


            $type = 'guardian';
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
                        'type' => SourceTypes::GUARDIAN_API,
                        'order' => ++$index,
                    ]
                );
            }

            DB::commit();

            $this->info('Articles from The Guardian successfully stored in database.');

        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error('FetchGuardianArticlesCommand error', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

        }
    }

}
