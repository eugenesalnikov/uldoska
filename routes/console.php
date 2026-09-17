<?php

Schedule::command('listings:expire')->dailyAt('06:00');
Schedule::command('sitemap:generate')->daily();
