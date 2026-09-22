<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ListingPhotosReady
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $listingId,
    )
    {
    }

}
