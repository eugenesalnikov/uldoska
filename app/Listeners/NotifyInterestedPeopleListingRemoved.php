<?php

namespace App\Listeners;

use App\Events\ListingRemoved;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

final readonly class NotifyInterestedPeopleListingRemoved
{
    public function handle(
        ListingRemoved $event,
    ): void
    {
        $title = $event->listing->title;

        foreach ($event->interestedChatIds as $chatId) {
            try {
                Telegram::sendMessage(
                    text: "Объявление «{$title}» снято, запрос больше не актуален.",
                    chat_id: $chatId,
                );
            } catch (TelegramException) {
                continue;
            }
        }
    }

}
