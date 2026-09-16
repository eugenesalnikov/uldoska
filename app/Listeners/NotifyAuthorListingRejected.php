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
        $reason = $listing->rejection_reason?->label() ?? 'не указана';

        $text = "Объявление «{$listing->title}» отклонено, причина – $reason";
        if (!blank($listing->rejection_comment)) {
            $text .= "\nКомментарий: $listing->rejection_comment";
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
