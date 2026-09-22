<?php

namespace App\Actions\PendingPhoto;

use App\Enums\PendingPhotoStatus;
use App\Models\PendingPhoto;

final readonly class CleanupExpiredPendingPhotosAction
{
    public function execute(): int
    {
        $photos = PendingPhoto::query()
            ->where('status', PendingPhotoStatus::Uploaded)
            ->where('created_at', '<', now()->subDay())
            ->get();

        $photos->each->delete();

        return $photos->count();
    }

}
