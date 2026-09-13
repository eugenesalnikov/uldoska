<?php

namespace App\Listeners;

use App\Events\ListingInterestRequested;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

final readonly class NotifyAuthorListingInterestRequested
{
    public function handle(ListingInterestRequested $event): void
    {
        $interest = $event->interest->load('listing');
        $listing = $interest->listing;
        $who = $interest->interested_name ?: 'Пользователь';

        Telegram::sendMessage(
            text: "По вашему объявлению «{$listing->title}» заинтересовались ($who). Отправить ему ваш контакт?",
            chat_id: $listing->telegram_chat_id,
            reply_markup: InlineKeyboardMarkup::make()->addRow(
                InlineKeyboardButton::make('Да', callback_data: "interest:accept:{$interest->id}"),
                InlineKeyboardButton::make('Нет', callback_data: "interest:decline:{$interest->id}"),
            )
        );
    }

}
