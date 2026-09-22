<?php

namespace App\Actions\PendingPhoto;

use App\Enums\PendingPhotoStatus;
use App\Models\PendingPhoto;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final readonly class ShowPendingPhotoAction
{
    public function execute(
        PendingPhoto $photo,
        string       $ownerToken,
    ): StreamedResponse
    {
        if (!$photo->isOwnedBy($ownerToken)) {
            abort(404);
        }

        return Storage::disk($photo->disk)->response(
            $photo->path,
            $photo->original_name,
        );
    }

}
