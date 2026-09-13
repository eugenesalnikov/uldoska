<?php

namespace App\Listeners;

use App\Events\ListingSubmittedForReview;
use Exception;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

final readonly class NotifyAdminListingAwaitingModeration
{
    public function handle(ListingSubmittedForReview $event): void
    {
        $adminChatId = config('services.telegram.admin_chat_id');

        if (blank($adminChatId)) {
            return;
        }

        $listing = $event->listing;
        $publicCode = $listing->public_code;
        $moderatorKey = config('uldoska.moderator_key');
        $moderationUrl = url("/mod/$publicCode?key=$moderatorKey");

        try {
            Telegram::sendMessage(
                text: implode("\n", [
                    'Новое объявление на модерации',
                    "«{$listing->title}»",
                    "Public ID: $publicCode",
                ]),
                chat_id: $adminChatId,
                reply_markup: InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make(
                        text: 'Открыть модерацию',
                        url: $moderationUrl,
                    ),
                ),
            );

        } catch (Exception $e) {
            report($e);
        }
    }

}
