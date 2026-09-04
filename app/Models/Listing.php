<?php

namespace App\Models;

use App\Enums\ListingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    /** @use HasFactory<\Database\Factories\ListingFactory> */
    use HasFactory, SoftDeletes, InteractsWithMedia;

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
            ->nonQueued();

        $this->addMediaConversion('show')
            ->width(1200)
            ->nonQueued();
    }

}
