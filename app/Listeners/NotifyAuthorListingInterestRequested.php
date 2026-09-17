<?php

namespace App\Listeners;

use App\Enums\ListingInterestStatus;
use App\Events\ListingInterestRequested;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

final readonly class NotifyAuthorListingInterestRequested
{
    public function handle(
        ListingInterestRequested $event,
    ): void
    {
        $interest = $event->interest->load('listing');
        $listing = $interest->listing;
        $interestedUsername = ltrim($event->interestedUsername, '@');
        $interestedName = $event->interestedName;

        $text = "По объявлению «{$listing->title}» интересуется @$interestedUsername";

        if (
            filled($interestedName)
            && mb_strtolower($interestedName) !== mb_strtolower($interestedUsername)
        ) {
            $text .= " ($interestedName)";
        }

        $text .= "\nhttps://t.me/$interestedUsername\n\n"
            . "Отправить ему ваш Telegram?\n"
            . "Можете написать сами – ссылка выше.";

        try {
            Telegram::sendMessage(
                text: $text,
                chat_id: $listing->telegram_chat_id,
                disable_web_page_preview: true,
                reply_markup: InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make('Отправить', callback_data: "interest:accept:$interest->id"),
                    InlineKeyboardButton::make('Не отправлять', callback_data: "interest:decline:$interest->id"),
                ),
            );
        } catch (TelegramException $e) {
            if ($this->authorUnreachable($e)) {
                $interest->update(['status' => ListingInterestStatus::Cancelled]);

                Telegram::sendMessage(
                    text: 'Автор сейчас недоступен в Telegram.',
                    chat_id: $interest->interested_chat_id,
                );

                return;
            }

            throw $e;
        }
    }

    private function authorUnreachable(TelegramException $e): bool
    {
        $message = mb_strtolower($e->getMessage());

        return str_contains($message, 'bot was blocked')
            || str_contains($message, 'user is deactivated')
            || str_contains($message, 'chat not found')
            || str_contains($message, 'forbidden');
    }

}
