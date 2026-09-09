<?php

namespace App\Models;

use App\Enums\ListingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'district_id',
    'category_id',
    'title',
    'body',
    'price',
    'phone',
    'status',
    'telegram_chat_id',
    'published_at',
    'expires_at',
])]
class Listing extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasUuids;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            $listing->manage_token ??= Str::random(64);
            $listing->status ??= ListingStatus::Pending;
        });
    }

    protected function casts(): array
    {
        return [
            'district_id'  => 'integer',
            'category_id'  => 'integer',
            'price'        => 'integer',
            'status'       => ListingStatus::class,
            'published_at' => 'datetime',
            'expires_at'   => 'datetime',
            'deleted_at'   => 'datetime',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === ListingStatus::Pending;
    }

    public function isReview(): bool
    {
        return $this->status === ListingStatus::Review;
    }

    public function isPublished(): bool
    {
        return $this->status === ListingStatus::Published;
    }

    public function isRemoved(): bool
    {
        return $this->status === ListingStatus::Removed;
    }

    public function canExtend(): bool
    {
        if (!$this->isPublished()) {
            return false;
        }

        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->lte(
            now()->addDays(config('uldoska.extend_within_days', 3))
        );
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', ListingStatus::Published)
            ->where(fn(Builder $q) => $q
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now())
            );
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::get(
            fn() => $this->getFirstMediaUrl('photos', 'thumb') ?: null
        );
    }

    protected function priceLabel(): Attribute
    {
        return Attribute::get(function () {
            if ($this->price === null) {
                return 'Договорная';
            }

            return number_format($this->price, 0, ',', ' ') . ' ₽';
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->onlyKeepLatest(8);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->format('webp')
            ->quality(80)
            ->nonQueued();

        $this->addMediaConversion('show')
            ->width(1200)
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }

}
