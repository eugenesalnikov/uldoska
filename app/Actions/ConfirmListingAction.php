<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Events\ListingSubmittedForReview;
use App\Exceptions\DomainException;
use App\Models\Listing;

class ConfirmListingAction
{
    /**
     * @throws DomainException
     */
    public function execute(
        string $manageToken,
        string $chatId,
    ): Listing
    {
        $listing = Listing::query()
            ->where('manage_token', $manageToken)
            ->first();

        if (!$listing) {
            throw new DomainException('Объявление не найдено.');
        }

        if ($listing->isBoundToTelegram() && !$listing->isOwnedByTelegram($chatId)) {
            throw new DomainException('Это объявление уже привязано к другому Telegram.');
        }

        if ($listing->isReview() && $listing->isOwnedByTelegram($chatId)) {
            return $listing;
        }

        if (!$listing->isPending()) {
            throw new DomainException('Это объявление уже нельзя подтвердить.');
        }

        $listing->update([
            'telegram_chat_id' => $chatId,
            'status'           => ListingStatus::Review,
        ]);

        ListingSubmittedForReview::dispatch($listing);

        return $listing;
    }

}
