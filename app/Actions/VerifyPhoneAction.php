<?php

namespace App\Actions;

use App\Events\ListingPhoneVerified;
use App\Exceptions\DomainException;
use App\Models\Listing;

final readonly class VerifyPhoneAction
{
    public function __construct(
        private GetListingForVerificationAction $getForVerification,
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
        $listing = $this->getForVerification->execute(
            $manageToken,
            $chatId
        );

        $listing->update([
            'telegram_chat_id'  => $chatId,
            'phone'             => $phone,
            'phone_verified_at' => now(),
        ]);

        ListingPhoneVerified::dispatch($listing->id);

        return $listing;
    }

}
