<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class NewsCommandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->command->confirm('Do you want to fetch news from all providers now?', true)) {
            $this->command->info('Fetching News...');

            $commands = [
                'news:fetch-news',
                'news:fetch-guardian',
                'news:fetch-new-york',
                'news:fetch-ai',
            ];

            foreach ($commands as $command) {
                $this->command->info("Running: {$command}");
                Artisan::call($command);
                $this->command->info(Artisan::output());
            }

            $this->command->info('All news fetched successfully.');
        } else {
            $this->command->info('Skipping news fetching...');
        }
    }
}
