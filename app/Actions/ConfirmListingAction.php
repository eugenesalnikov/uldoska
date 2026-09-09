<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Models\Listing;
use RuntimeException;

class ConfirmListingAction
{
    public function execute(string $manageToken, string $chatId): Listing
    {
        $listing = Listing::query()
            ->where('manage_token', $manageToken)
            ->first();

        if (!$listing) {
            throw new RuntimeException('Объявление не найдено.');
        }

        if ($listing->isReview() && $listing->telegram_chat_id === $chatId) {
            return $listing;
        }

        if (!$listing->isPending()) {
            throw new RuntimeException('Это объявление уже нельзя подтвердить.');
        }

        $listing->update([
            'telegram_chat_id' => $chatId,
            'status'           => ListingStatus::Review,
        ]);

        return $listing;
    }

}
