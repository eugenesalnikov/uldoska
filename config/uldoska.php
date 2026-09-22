<?php

return [
    'extend_within_days'         => 3,
    'max_published_listings'     => 5,
    'listing_public_code_length' => env('LISTING_PUBLIC_CODE_LENGTH', 8),
    'max_attached_photos_count'  => 8,
    'max_attached_photo_size'    => 5120, // (kilobytes)
    'max_pending_interests'      => 10,
    'listing_ttl_days'           => 14,
    'telegram_bot'               => env('TELEGRAM_BOT_USERNAME', 'uldoska_test_bot'),
    'moderator_key'              => env('MODERATOR_KEY'),
    'restricted_district_names'  => ['all', 'r', 'c', 'listings', 'admin'],
];
