<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


for ($i = 0; $i < 5; $i++) {
    Schedule::command('blog:generate')->dailyAt('15:00');
}
