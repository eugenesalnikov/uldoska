<?php

namespace App\Http\Controllers;

use App\Actions\PendingPhoto\DeletePendingPhotoAction;
use App\Actions\PendingPhoto\ShowPendingPhotoAction;
use App\Actions\PendingPhoto\StorePendingPhotoAction;
use App\Enums\PendingPhotoStatus;
use App\Http\Requests\StorePendingPhotoRequest;
use App\Models\PendingPhoto;
use App\Services\PhotoOwnerToken;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PendingPhotoController extends Controller
{
    public function __construct(
        private readonly PhotoOwnerToken $ownerToken,
    )
    {
    }

    public function store(
        StorePendingPhotoRequest $request,
        StorePendingPhotoAction  $action,
    ): Response
    {
        $photo = $action->execute(
            $request->toData(),
            $this->ownerToken->get(),
        );

        return response($photo->uuid, 200);
    }

    public function destroy(
        PendingPhoto             $pendingPhoto,
        DeletePendingPhotoAction $action,
    ): Response
    {
        $action->execute(
            $pendingPhoto,
            $this->ownerToken->get(),
        );

        return response()->noContent();
    }

    public function show(
        PendingPhoto           $pendingPhoto,
        ShowPendingPhotoAction $action,
    ): StreamedResponse
    {
        return $action->execute(
            $pendingPhoto,
            $this->ownerToken->get(),
        );
    }

}
