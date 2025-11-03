<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('news:fetch-news')
    ->cron('0 */6 * * *')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('news:fetch-guardian')
    ->cron('10 */6 * * *')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('news:fetch-new-york')
    ->cron('20 */6 * * *')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('news:fetch-ai')
    ->cron('30 */6 * * *')
    ->withoutOverlapping()
    ->runInBackground();
