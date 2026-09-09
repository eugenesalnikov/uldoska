<?php

return [

    'listing_ttl_days' => 14,

    'extend_within_days' => 3,

    'telegram_bot' => env('TELEGRAM_BOT', 'uldoska_bot'),

    'restricted_district_names' => ['all', 'r', 'c', 'listings', 'admin'],

    'moderator_key' => env('MODERATOR_KEY'),

];
