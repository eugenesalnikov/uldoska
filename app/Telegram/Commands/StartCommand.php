<?php

namespace App\Telegram\Commands;

use App\Actions\ConfirmListingAction;
use App\Exceptions\DomainException;
use SergiX44\Nutgram\Nutgram;

readonly class StartCommand
{
    public function __construct(
        private ConfirmListingAction $confirm,
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

        try {
            $listing = $this->confirm->execute(
                (string)$token,
                (string)$bot->chatId(),
            );
        } catch (DomainException $e) {
            $bot->sendMessage($e->getMessage());
            return;
        }

        if (!$listing->wasChanged()) {
            $bot->sendMessage("Объявление «{$listing->title}» уже подтверждено и находится на модерации.");
        }
    }

    public function interest(Nutgram $bot, string $token): void
    {

    }

    public function invalid(Nutgram $bot, string $token): void
    {
        $bot->sendMessage('Некорректная ссылка.');
    }

}
