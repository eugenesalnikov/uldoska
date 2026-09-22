<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('media-library:cleanup-temp')]
#[Description('Удаляет старые временные папки Spatie Media Library')]
class CleanupMediaLibraryTempCommand extends Command
{
    protected $signature = 'media-library:cleanup-temp';

    protected $description = 'Удаляет старые временные папки Spatie Media Library';

    public function handle(): int
    {
        $root = storage_path('media-library/temp');

        if (!File::isDirectory($root)) {
            $this->info('Папка temp отсутствует.');

            return self::SUCCESS;
        }

        $threshold = now()->subHours(6)->getTimestamp();
        $deleted = 0;

        foreach (File::directories($root) as $directory) {
            if (File::lastModified($directory) > $threshold) {
                continue;
            }

            File::deleteDirectory($directory);
            $deleted++;
        }

        $this->info("Удалено временных папок: {$deleted}");

        return self::SUCCESS;
    }

}
