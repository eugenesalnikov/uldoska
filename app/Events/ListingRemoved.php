<?php

namespace App\Events;

use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ListingRemoved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Listing    $listing,
        public Collection $interestedChatIds,
    )
    {
    }

}
