<?php

namespace App\Listeners;

use App\Events\ListingInterestAccepted;
use Nutgram\Laravel\Facades\Telegram;

final readonly class NotifyInterestedPersonListingInterestAccepted
{
    public function handle(ListingInterestAccepted $event): void
    {
        $interest = $event->interest->loadMissing('listing');
        $listing = $interest->listing;
        $chatId = $interest->interested_chat_id;

        Telegram::sendMessage(
            text: "Автор объявления «{$listing->title}» согласился поделиться контактом. Не переводите предоплату до личной встречи и осмотра товара!",
            chat_id: $chatId,
        );

        if (filled($listing->phone)) {
            Telegram::sendContact(
                phone_number: $listing->phone,
                first_name: $listing->author_name ?: 'Автор объявления',
                chat_id: $chatId,
            );
        }

        if (blank($listing->phone)) {
            Telegram::sendMessage(
                text: 'Автор подтвердил интерес, но контакт в профиле не указан.',
                chat_id: $chatId,
            );
        }
    }

}
