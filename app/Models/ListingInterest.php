<?php

namespace App\Models;

use App\Enums\ListingInterestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'listing_id',
    'interested_chat_id',
    'status',
])]
class ListingInterest extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ListingInterestStatus::class,
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

}
