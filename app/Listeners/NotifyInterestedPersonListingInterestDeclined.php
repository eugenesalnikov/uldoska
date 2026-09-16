<?php

namespace App\Listeners;

use App\Events\ListingInterestDeclined;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyInterestedPersonListingInterestDeclined
{
    public function handle(ListingInterestDeclined $event): void
    {
        $interest = $event->interest->loadMissing('listing');

        try {
            Telegram::sendMessage(
                text: "Автор объявления «{$interest->listing->title}» предпочёл не делиться контактом.",
                chat_id: $interest->interested_chat_id,
            );
        } catch (Exception $e) {
            report($e);
        }
    }

}
