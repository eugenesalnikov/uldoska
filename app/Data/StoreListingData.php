<?php

namespace App\Data;

final readonly class StoreListingData
{
    /**
     * @param list<string> $photoUuids
     */
    public function __construct(
        public ?int   $districtId,
        public int    $categoryId,
        public string $title,
        public string $body,
        public ?int   $price,
        public array  $photoUuids,
        public string $ownerToken,
    )
    {
    }

}
