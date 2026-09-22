<?php

Schedule::command('listings:expire')->dailyAt('06:00');
Schedule::command('sitemap:generate')->daily();
Schedule::command('photos:cleanup-pending')->hourly();
Schedule::command('media-library:cleanup-temp')->hourly();
Schedule::command('listings:try-publish')->everyMinute();
