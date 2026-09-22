<?php

namespace App\Data;

final readonly class UploadedPhotoData
{
    public function __construct(
        public string $path,
        public string $originalName,
        public string $mime,
        public int    $size,
        public string $disk = 'local',
    )
    {
    }
}
