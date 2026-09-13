<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property int $sort
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Listing> $listings
 * @property-read int|null $listings_count
 * @property-read Category|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category roots()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category withoutTrashed()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @dontforget restricted_district_names - добавить в будущем проверку на запрещенные названия для районов
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $sort
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Listing> $listings
 * @property-read int|null $listings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District active()
 * @method static \Database\Factories\DistrictFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District withoutTrashed()
 */
	class District extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $district_id
 * @property int $category_id
 * @property string $title
 * @property string $body
 * @property int|null $price
 * @property string $phone
 * @property \App\Enums\ListingStatus $status
 * @property string $manage_token
 * @property string|null $telegram_chat_id
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \App\Enums\ListingRejectionReason|null $rejection_reason
 * @property string|null $rejection_comment
 * @property string $public_code
 * @property-read \App\Models\Category|null $category
 * @property-read mixed $cover_url
 * @property-read \App\Models\District|null $district
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $price_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Listing> $telegramListings
 * @property-read int|null $telegram_listings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing dueToExpire()
 * @method static \Database\Factories\ListingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing ownedByTelegram(string $chatId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereManageToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing wherePublicCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereRejectionComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereTelegramChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing withoutTrashed()
 */
	class Listing extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $listing_id
 * @property string $interested_chat_id
 * @property string|null $interested_username
 * @property string|null $interested_name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Listing|null $listing
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereInterestedChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereInterestedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereInterestedUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereListingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListingInterest whereUpdatedAt($value)
 */
	class ListingInterest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

