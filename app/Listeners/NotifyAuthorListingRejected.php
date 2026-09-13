<?php

namespace App\Listeners;

use App\Events\ListingRejected;
use Exception;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyAuthorListingRejected
{
    public function handle(ListingRejected $event): void
    {
        $listing = $event->listing;
        $chatId = $listing->telegram_chat_id;

        if (blank($chatId)) {
            return;
        }

        $submitLink = route('listings.create');

        $text = "Объявление «{$listing->title}» отклонено, причина — {$listing->rejection_reason->label()}";
        if (!blank($listing->rejection_comment)) {
            $text .= "\nКомментарий: {$listing->rejection_comment}";
        }
        $text .= "\nПодайте объявление заново по ссылке $submitLink";

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
