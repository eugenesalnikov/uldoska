<?php

namespace App\Support;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class UuidFileNamer extends FileNamer
{
    public function originalFileName(string $fileName): string
    {
        return (string)Str::uuid();
    }

    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $base = pathinfo($fileName, PATHINFO_FILENAME);

        return "{$base}-{$conversion->getName()}";
    }

    public function responsiveFileName(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }

}
