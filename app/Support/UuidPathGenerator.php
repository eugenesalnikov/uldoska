<?php

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class UuidPathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $prefix = config('medialibrary.paths.prefix', '');

        return $prefix !== ''
            ? $prefix . '/' . $media->uuid
            : $media->uuid;
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/c/';
    }

}
