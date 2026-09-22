<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ListingPublishSlotFreed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $telegramChatId,
    )
    {
    }

}
