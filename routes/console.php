<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('news:fetch-news')
    //For rapid implementation
//    ->cron('0 */6 * * *')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('news:fetch-guardian')
    //For rapid implementation
//    ->cron('10 */6 * * *')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('news:fetch-new-york')
    //For rapid implementation
//    ->cron('20 */6 * * *')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('news:fetch-ai')
    //For rapid implementation
//    ->cron('30 */6 * * *')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
