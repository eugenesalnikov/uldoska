<?php

namespace App\Data;

final readonly class StoreListingData
{
    public function __construct(
        public ?int   $districtId,
        public int    $categoryId,
        public string $title,
        public string $body,
        public ?int   $price,
        /** @var list<string> $photoPaths абсолютные пути к временным файлам */
        public array  $photoPaths,
    )
    {
    }

}
