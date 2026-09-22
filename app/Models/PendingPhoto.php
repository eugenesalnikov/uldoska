<?php

namespace App\Models;

use App\Enums\PendingPhotoStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'owner_token',
    'disk',
    'path',
    'original_name',
    'mime',
    'size',
    'status',
    'listing_id',
])]
class PendingPhoto extends Model
{
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'size'   => 'integer',
            'status' => PendingPhotoStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PendingPhoto $photo) {
            $photo->uuid ??= (string)Str::uuid();
        });

        static::deleting(function (PendingPhoto $photo) {
            Storage::disk($photo->disk)->delete($photo->path);
        });
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function absolutePath(): string
    {
        return Storage::disk($this->disk)->path($this->path);
    }

    public function isOwnedBy(string $ownerToken): bool
    {
        return $this->owner_token === $ownerToken
            && $this->status === PendingPhotoStatus::Uploaded;
    }

}
