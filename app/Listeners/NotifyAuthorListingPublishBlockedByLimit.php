<?php

namespace App\Listeners;

use App\Events\ListingPublishBlockedByLimit;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyAuthorListingPublishBlockedByLimit
{
    public function handle(ListingPublishBlockedByLimit $event): void
    {
        $listing = $event->listing;
        $chatId = $listing->telegram_chat_id;

        if (blank($chatId)) {
            return;
        }

        $limit = config('uldoska.max_published_listings', 5);

        $text = implode("\n", [
            "Объявление «{$listing->title}» не может быть опубликовано, т.к. у вас исчерпан лимит в $limit объявлений на доске.",
            "Получить список ваших объявлений – команда /my.",
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
