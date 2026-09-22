<?php

namespace App\Actions\PendingPhoto;

use App\Data\UploadedPhotoData;
use App\Enums\PendingPhotoStatus;
use App\Models\PendingPhoto;

final readonly class StorePendingPhotoAction
{
    public function execute(
        UploadedPhotoData $data,
        string            $ownerToken,
    ): PendingPhoto
    {
        return PendingPhoto::query()->create([
            'owner_token'   => $ownerToken,
            'disk'          => $data->disk,
            'path'          => $data->path,
            'original_name' => $data->originalName,
            'mime'          => $data->mime,
            'size'          => $data->size,
            'status'        => PendingPhotoStatus::Uploaded,
        ]);
    }

}
