<?php

namespace App\Telegram\Commands;

use App\Actions\GetListingForVerificationAction;
use App\Actions\ListingInterest\SendInterestToListing;
use App\Events\ListingInterestRequested;
use App\Exceptions\DomainException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

final readonly class StartCommand
{
    public function __construct(
        private GetListingForVerificationAction $getForVerification,
        private SendInterestToListing           $sendInterest,
    )
    {
    }

    public function welcome(Nutgram $bot): void
    {
        $bot->sendMessage(
            'Откройте ссылку с сайта, чтобы подтвердить объявление или откликнуться на него.'
        );
    }

    public function verifyPhone(Nutgram $bot, string $token): void
    {
        if (blank($token)) {
            $bot->sendMessage('Чтобы подтвердить объявление, перейдите по ссылке с сайта.');
            return;
        }

        try {
            $listing = $this->getForVerification->execute(
                $token,
                (string)$bot->chatId(),
            );
        } catch (DomainException $e) {
            $bot->sendMessage($e->getMessage());

            return;
        }

        $bot->setUserData('verification_token', $token);

        $bot->sendMessage(
            "Чтобы подтвердить «{$listing->title}», привяжите номер из Telegram. Коды мы не просим. Номер не покажем на карточке и никому не передадим.",
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
                one_time_keyboard: true,
            )
                ->addRow(
                    KeyboardButton::make(
                        'Привязать номер телефона',
                        request_contact: true,
                    )
                )
        );
    }

    public function interest(Nutgram $bot, string $token): void
    {
        if (blank($bot->user()?->username)) {
            $bot->sendMessage('Чтобы откликнуться, укажите username в настройках Telegram и перейди по ссылке ещё раз.');
            return;
        }

        try {
            $interest = $this->sendInterest->execute(
                $token,
                (string)$bot->chatId(),
            );
        } catch (DomainException $e) {
            $bot->sendMessage($e->getMessage());
            return;
        }

        $bot->sendMessage('Передаём запрос автору. Если ответит – напишем сюда.');

        ListingInterestRequested::dispatch(
            $interest,
            $bot->user()?->username,
            trim(($bot->user()->first_name ?? '') . ' ' . ($bot->user()->last_name ?? '')) ?: null,
        );
    }

    public function invalid(Nutgram $bot, string $token): void
    {
        $bot->sendMessage('Некорректная ссылка.');
    }

}
