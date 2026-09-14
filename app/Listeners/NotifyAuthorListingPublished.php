<?php

namespace App\Listeners;

use App\Events\ListingPublished;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyAuthorListingPublished
{
    public function handle(ListingPublished $event): void
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

        $text = implode("\n", [
            "Объявление «{$listing->title}» опубликовано.",
            "",
            "Ссылка на управление объявлением: $managementLink",
            "Ссылкой для управления ни с кем не делитесь — по ней можно снять или изменить объявление.",
            "Ссылка является одноразовой. Она обновится после первого перехода по ней. Новая ссылка будет тут — /my",
            "А это — публичная ссылка на объявление: {$listing->publicLink()}, ею можно делиться."
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
