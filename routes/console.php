<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('news:fetch-due-sources')->everyMinute()->withoutOverlapping();

Schedule::job(new \App\Jobs\PoolSelectionJob)->everyMinute()->withoutOverlapping();

Schedule::job(new \App\Jobs\AIQueueJob)->everyMinute()->withoutOverlapping();
