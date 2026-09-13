<?php

namespace App\Actions;

use App\Exceptions\DomainException;
use App\Models\Listing;

final readonly class GetListingForConfirmationAction
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

        if (
            $listing->isBoundToTelegram()
            && !$listing->isOwnedByTelegram($chatId)
        ) {
            throw new DomainException(
                'Это объявление уже привязано к другому Telegram.'
            );
        }

        if (
            $listing->isReview()
            && $listing->isOwnedByTelegram($chatId)
        ) {
            throw new DomainException('Объявление уже подтверждено и находится на модерации.');
        }

        if (!$listing->isPending()) {
            throw new DomainException(
                'Это объявление уже нельзя подтвердить.'
            );
        }

        return $listing;
    }

}
