<?php

namespace App\Models;

use App\Enums\ListingRejectionReason;
use App\Enums\ListingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
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
    'rejection_reason',
    'rejection_comment',
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
            'district_id'      => 'integer',
            'category_id'      => 'integer',
            'price'            => 'integer',
            'status'           => ListingStatus::class,
            'published_at'     => 'datetime',
            'expires_at'       => 'datetime',
            'deleted_at'       => 'datetime',
            'rejection_reason' => ListingRejectionReason::class,
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

    public function isRejected(): bool
    {
        return $this->status === ListingStatus::Rejected;
    }

    public function isExpired(): bool
    {
        return $this->status === ListingStatus::Expired;
    }

    public function canExtend(): bool
    {
        if ($this->isRemoved() || $this->isRejected()) {
            return false;
        }

        if ($this->isPublished()) {
            return $this->expires_at->lte(
                now()->addDays(config('uldoska.extend_within_days', 3))
            ) ?? false;
        }

        return $this->isExpired();
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function telegramListings(): HasMany
    {
        return $this->hasMany(self::class, 'telegram_chat_id', 'telegram_chat_id');
    }

    #[Scope]
    public function published(Builder $query): Builder
    {
        return $query
            ->where('status', ListingStatus::Published)
            ->where(fn(Builder $q) => $q
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now())
            );
    }

    #[Scope]
    public function dueToExpire(Builder $query): Builder
    {
        return $query
            ->where('status', ListingStatus::Published)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    #[Scope]
    public function ownedByTelegram(Builder $query, string $chatId): Builder
    {
        return $query->where('telegram_chat_id', $chatId);
    }

    public function publishedCountForTelegram(): int
    {
        return static::query()
            ->where('telegram_chat_id', $this->telegram_chat_id)
            ->published()
            ->count();
    }

    /**
     * @return Collection<Listing>
     */
    public function publishedListingsForTelegram(): Collection
    {
        return static::query()
            ->where('telegram_chat_id', $this->telegram_chat_id)
            ->published()
            ->get();
    }

    public function hasReachedPublishedLimit(): bool
    {
        return $this->publishedCountForTelegram() >= config('uldoska.max_published_listings', 5);
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

    public function telegramUrl(): string
    {
        $username = config('uldoska.telegram_bot', env('TELEGRAM_BOT_USERNAME'));

        return "https://t.me/$username?start=$this->manage_token";
    }

    public function isBoundToTelegram(): bool
    {
        return filled($this->telegram_chat_id);
    }

    public function isOwnedByTelegram(int|string $chatId): bool
    {
        return $this->telegram_chat_id !== null
            && (string)$this->telegram_chat_id === (string)$chatId;
    }

}
