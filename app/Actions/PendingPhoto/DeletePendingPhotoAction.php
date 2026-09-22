<?php

namespace App\Actions\PendingPhoto;

use App\Enums\PendingPhotoStatus;
use App\Models\PendingPhoto;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final readonly class DeletePendingPhotoAction
{
    public function execute(
        PendingPhoto $photo,
        string       $ownerToken,
    ): void
    {
        if (!$photo->isOwnedBy($ownerToken)) {
            throw new AccessDeniedHttpException();
        }

        $photo->delete();
    }

}
