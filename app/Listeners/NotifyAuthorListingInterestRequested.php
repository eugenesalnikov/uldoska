<?php

namespace App\Listeners;

use App\Events\ListingInterestRequested;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

final readonly class NotifyAuthorListingInterestRequested
{
    public function handle(ListingInterestRequested $event): void
    {
        $interest = $event->interest->load('listing');
        $listing = $interest->listing;
        $who = $interest->interested_name ?: 'Пользователь';

        try {
            Telegram::sendMessage(
                text: "По объявлению «{$listing->title}» интересуется $who.\nОтправить ему ваш номер телефона?\nНомер не появится на карточке — только в этом чате.",
                chat_id: $listing->telegram_chat_id,
                reply_markup: InlineKeyboardMarkup::make()->addRow(
                    InlineKeyboardButton::make('Отправить номер', callback_data: "interest:accept:$interest->id"),
                    InlineKeyboardButton::make('Не отправлять', callback_data: "interest:decline:$interest->id"),
                ),
            );
        } catch (TelegramException $e) {
            if ($this->authorUnreachable($e)) {
                $interest->delete();

                Telegram::sendMessage(
                    text: 'Автор сейчас недоступен в Telegram.',
                    chat_id: $interest->interested_chat_id,
                );

                return;
            }

            throw $e;
        }

        Telegram::sendMessage(
            text: 'Запрос отправлен автору объявления. Если он согласится, вы получите контакт. Не переводите предоплату до личной встречи и осмотра товара!',
            chat_id: $interest->interested_chat_id,
        );
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
