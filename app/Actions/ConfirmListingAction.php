<?php

namespace App\Actions;

use App\Enums\ListingStatus;
use App\Events\ListingSubmittedForReview;
use App\Exceptions\DomainException;
use App\Models\Listing;

final readonly class ConfirmListingAction
{
    public function __construct(
        private GetListingForConfirmationAction $getForConfirmation,
    )
    {
    }

    /**
     * @throws DomainException
     */
    public function execute(
        string $manageToken,
        string $chatId,
        string $phone,
    ): Listing
    {
        $listing = $this->getForConfirmation->execute(
            $manageToken,
            $chatId
        );

        $listing->update([
            'telegram_chat_id' => $chatId,
            'status'           => ListingStatus::Review,
            'phone'            => $phone,
        ]);

        ListingSubmittedForReview::dispatch($listing);

        return $listing;
    }

}
