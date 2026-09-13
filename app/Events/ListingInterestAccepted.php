<?php

namespace App\Events;

use App\Models\ListingInterest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ListingInterestAccepted
{
    use Dispatchable, SerializesModels;

    public function __construct(public ListingInterest $interest)
    {
    }
}
