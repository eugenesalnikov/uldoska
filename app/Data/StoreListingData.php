<?php

namespace App\Data;

readonly class StoreListingData
{
    public function __construct(
        public int    $districtId,
        public int    $categoryId,
        public string $title,
        public string $body,
        public ?int   $price,
        public string $phone,
        /** @param list<string> $photoPaths абсолютные пути к временным файлам */
        public array  $photoPaths,
    )
    {
    }

}
