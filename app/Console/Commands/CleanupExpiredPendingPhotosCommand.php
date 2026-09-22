<?php

namespace App\Console\Commands;

use App\Actions\PendingPhoto\CleanupExpiredPendingPhotosAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('photos:cleanup-pending')]
#[Description('Удаляет незакреплённые pending-фото старше суток')]
class CleanupExpiredPendingPhotosCommand extends Command
{
    public function handle(CleanupExpiredPendingPhotosAction $action): int
    {
        $deleted = $action->execute();

        $this->info("Удалено pending-фото: {$deleted}");

        return self::SUCCESS;
    }

}
