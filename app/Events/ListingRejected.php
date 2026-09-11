<?php

namespace App\Events;

use App\Models\Listing;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Listing $listing,
    )
    {
    }

}
