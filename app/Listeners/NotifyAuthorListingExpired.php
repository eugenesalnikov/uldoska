<?php

namespace App\Listeners;

use App\Events\ListingExpired;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

class NotifyAuthorListingExpired
{
    public function handle(ListingExpired $event): void
    {
        $listing = $event->listing;
        $chatId = $listing->telegram_chat_id;

        if (blank($chatId)) {
            return;
        }

        $managementLink = route('listings.manage', $listing);

        $text = implode("\n", [
            "Объявление «{$listing->title}» скрыто с доски, т.к. закончился срок размещения в 14 дней.",
            "",
            "Его можно продлить по ссылке управления: $managementLink",
            "Ссылкой для управления ни с кем не делитесь — по ней можно снять или изменить объявление."
        ]);

        try {
            Telegram::sendMessage(
                text: $text,
                chat_id: $chatId,
            );
        } catch (Exception $e) {
            report($e);
        }
    }

}
