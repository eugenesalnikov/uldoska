<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('listings:expire')->dailyAt('06:00');
Schedule::command('sitemap:generate')->daily();
