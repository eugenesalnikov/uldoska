<?php

namespace App\Listeners;

use App\Events\ListingSubmittedForReview;
use Illuminate\Support\Facades\Log;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Throwable;

class NotifyAdminListingAwaitingModeration
{
    public function handle(ListingSubmittedForReview $event): void
    {
        $adminChatId = config('services.telegram.admin_chat_id');

        if (blank($adminChatId)) {
            return;
        }

        $listing = $event->listing;
        $uuid = $listing->uuid;
        $moderatorKey = config('uldoska.moderator_key');
        $moderationUrl = url("/mod/$uuid?key=$moderatorKey");

        try {

            Telegram::sendMessage(
                text: implode("\n", [
                    'Новое объявление на модерации',
                    "«{$listing->title}»",
                    "UUID: $uuid",
                ]),
                chat_id: $adminChatId,
                reply_markup: InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make(
                        text: 'Открыть модерацию',
                        url: $moderationUrl,
                    ),
                ),
            );


        } catch (Throwable $e) {
            report($e);
            Log::warning('Произошла ошибка при уведомлении администратора о необходимости провести модерацию нового объявления', [
                'listing_id' => $listing->id,
                'error' => $e->getMessage(),
            ]);
        }

    }

}
