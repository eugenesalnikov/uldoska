<?php

namespace App\Listeners;

use App\Events\ListingPublishBlockedByLimit;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

class NotifyAuthorListingPublishBlockedByLimit
{
    public function handle(ListingPublishBlockedByLimit $event): void
    {
        $listing = $event->listing;
        $chatId = $listing->telegram_chat_id;

        if (blank($chatId)) {
            return;
        }

        $text = implode("\n", [
            "Объявление «{$listing->title}» не может быть опубликовано, т.к. у вас исчерпан лимит в 5 объявлений на доске.",
            "Получить список ваших объявлений - команда /my.",
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
