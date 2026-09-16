<?php

namespace App\Listeners;

use App\Events\ListingExpired;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyAuthorListingExpired
{
    public function handle(ListingExpired $event): void
    {
        $listing = $event->listing;
        $chatId = $listing->telegram_chat_id;

        if (blank($chatId)) {
            return;
        }

        $managementLink = route('listings.manage', [
            'listing' => $listing,
            'key'     => $listing->manage_token,
        ]);

        $days = config('uldoska.listing_ttl_days', 14);

        $text = implode("\n", [
            "Объявление «{$listing->title}» скрыто с доски, т.к. закончился срок размещения в $days дней.",
            "",
            "Его можно продлить по ссылке управления: $managementLink",
            "Ссылкой для управления ни с кем не делитесь – по ней можно снять или изменить объявление."
        ]);

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
