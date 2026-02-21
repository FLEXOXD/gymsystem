<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:notify')->dailyAt('08:00');
Schedule::command('maintenance:cleanup-files --days=14')->dailyAt('03:20');
Schedule::command('attendances:archive --months=2')->dailyAt('03:40')->withoutOverlapping();
