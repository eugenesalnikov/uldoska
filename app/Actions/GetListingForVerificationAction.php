<?php

namespace App\Actions;

use App\Exceptions\DomainException;
use App\Models\Listing;

final readonly class GetListingForVerificationAction
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
            throw new DomainException('Ссылка недействительна или объявление уже удалено.');
        }

        if ($listing->isRemoved() || $listing->isRejected()) {
            throw new DomainException('Это объявление уже нельзя подтвердить.');
        }

        if (
            $listing->isBoundToTelegram()
            && !$listing->isOwnedByTelegram($chatId)
        ) {
            throw new DomainException('Это объявление уже привязано к другому Telegram.');
        }

        if ($listing->hasVerifiedPhone()) {
            throw new DomainException('Номер для этого объявления уже подтверждён.');
        }

        return $listing;
    }

}
