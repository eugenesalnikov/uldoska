<?php

namespace App\Listeners;

use App\Events\ListingInterestDeclined;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyInterestedPersonListingInterestDeclined
{
    public function handle(ListingInterestDeclined $event): void
    {
        $interest = $event->interest->loadMissing('listing');

        Telegram::sendMessage(
            text: "Автор объявления «{$interest->listing->title}» предпочёл не делиться контактом.",
            chat_id: $interest->interested_chat_id,
        );
    }

}
