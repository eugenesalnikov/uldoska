<?php

namespace App\Listeners;

use App\Events\ListingInterestAccepted;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyInterestedPersonListingInterestAccepted
{
    public function handle(
        ListingInterestAccepted $event,
    ): void
    {
        $interest = $event->interest->loadMissing('listing');
        $listing = $interest->listing;
        $chatId = $interest->interested_chat_id;
        $authorUsername = ltrim($event->authorUsername, '@');

        $authorName = $event->authorName;

        $text = "Автор объявления «{$listing->title}» согласился поделиться своим Telegram.\n"
            . "Не переводите предоплату до личной встречи и осмотра.\n\n"
            . "Telegram: https://t.me/$authorUsername";

        if (filled($authorName)) {
            $text .= "\nИмя: $authorName";
        }

        try {
            Telegram::sendMessage(
                text: $text,
                chat_id: $chatId,
                disable_web_page_preview: true,
            );

        } catch (Exception $e) {
            report($e);
        }
    }

}
