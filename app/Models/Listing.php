<?php

namespace App\Models;

use App\Enums\ListingRejectionReason;
use App\Enums\ListingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
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
    use HasFactory, SoftDeletes, InteractsWithMedia, HasUniqueStringIds;

    public function uniqueIds(): array
    {
        return ['public_code'];
    }

    public function getRouteKeyName(): string
    {
        return 'public_code';
    }

    public function newUniqueId(): string
    {
        do {
            $code = Str::random(config('uldoska.listing_public_code_length', 8));
        } while (static::query()->where('public_code', $code)->exists());

        return $code;
    }

    protected function isValidUniqueId(mixed $value): bool
    {
        return is_string($value) && preg_match('/^[a-zA-Z0-9]{8}$/', $value) === 1;
    }

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            $listing->manage_token ??= Str::random(32);
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
        return $this->status === ListingStatus::Published
            && ($this->expires_at === null || $this->expires_at->isFuture());
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
        return $this->status === ListingStatus::Expired
            || ($this->status === ListingStatus::Published
                && $this->expires_at?->isPast());
    }

    public function canExtend(): bool
    {
        if ($this->isRemoved() || $this->isRejected()) {
            return false;
        }

        if ($this->isPublished()) {
            return $this->expires_at?->lte(
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
        $username = config('uldoska.telegram_bot');

        return "https://t.me/$username?start=c_$this->manage_token";
    }

    public function telegramInterestLink(): string
    {
        $username = config('uldoska.telegram_bot');

        return "https://t.me/$username?start=i_$this->public_code";
    }

    public function publicLink(): string
    {
        return route('listings.show', $this);
    }

    public function datePublishedPlainFormat(): string
    {
        return $this->published_at->translatedFormat($this->published_at->isCurrentYear() ? 'G:i, j F' : 'G:i, j F Y');
    }

    public function datePublishedHumanreadable(): string
    {
        return $this->published_at->diffInDays(now()) < 7
            ? $this->published_at->diffForHumans()
            : $this->published_at->translatedFormat($this->published_at->isCurrentYear() ? 'j F' : 'j F Y');
    }

    public function seoDescription(): string
    {
        return Str::limit($this->body, 150);
    }

    public function statusIn(array $statuses): bool
    {
        return in_array($this->status, $statuses, true);
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
