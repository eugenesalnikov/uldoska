<?php

namespace App\Telegram\Commands;

use App\Actions\ConfirmListingAction;
use App\Actions\GetListingForConfirmationAction;
use App\Actions\SendInterestToListing;
use App\Exceptions\DomainException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

final readonly class StartCommand
{
    public function __construct(
        private GetListingForConfirmationAction $getForConfirmation,
        private SendInterestToListing           $sendInterest,
    )
    {
    }

    public function welcome(Nutgram $bot): void
    {
        $bot->sendMessage(
            'Привет. Откройте ссылку с сайта, чтобы подтвердить объявление или откликнуться на него.'
        );
    }

    public function confirm(Nutgram $bot, string $token): void
    {
        if (blank($token)) {
            $bot->sendMessage('Чтобы подтвердить объявление, перейдите по ссылке с сайта.');
            return;
        }

        $pendingToken = $bot->getUserData('confirmation_token');

        if (filled($pendingToken)) {
            $bot->sendMessage(
                'У вас уже есть объявление, ожидающее подтверждения. '
                . 'Сначала поделитесь номером телефона для него.'
            );
            return;
        }

        try {
            $listing = $this->getForConfirmation->execute(
                $token,
                (string)$bot->chatId(),
            );
        } catch (DomainException $e) {
            $bot->sendMessage($e->getMessage());

            return;
        }

        $bot->setUserData('confirmation_token', $token);

        $bot->sendMessage(
            "Чтобы подтвердить объявление «{$listing->title}», "
            . 'привяжите к нему свой номер телефона из Telegram. Бот не запрашивает у вас никаких секретных кодов!',
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

        /*if (!$listing->wasChanged()) {
            $bot->sendMessage("Объявление «{$listing->title}» уже подтверждено и находится на модерации.");
        }*/
    }

    public function interest(Nutgram $bot, string $token): void
    {
        try {
            $this->sendInterest->execute(
                $token,
                (string)$bot->chatId(),
                $bot->user()?->username,
                trim(($bot->user()?->first_name ?? '') . ' ' . ($bot->user()?->last_name ?? '')) ?: null,
            );
        } catch (DomainException $e) {
            $bot->sendMessage($e->getMessage());
            return;
        }

        $bot->sendMessage('Запрос отправлен автору объявления. Если он согласится, вы получите контакт.');
    }

    public function invalid(Nutgram $bot, string $token): void
    {
        $bot->sendMessage('Некорректная ссылка.');
    }

}
