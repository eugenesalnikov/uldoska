<?php

namespace App\Exceptions;

use App\Models\Listing;

class ListingActiveLimitReachedException extends DomainException
{
    public function __construct(
        public Listing $listing,
    )
    {
        parent::__construct('Можно держать не больше 5 объявлений на доске одновременно.');
    }

}
