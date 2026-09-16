<?php

namespace App\Events;

use App\Models\ListingInterest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ListingInterestRequested
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ListingInterest $interest,
        public ?string         $interestedUsername = null,
        public ?string         $interestedName = null,
    )
    {
    }
}
