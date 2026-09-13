<?php

namespace App\Telegram\Commands;

use App\Actions\RespondToListingInterest;
use App\Exceptions\DomainException;
use SergiX44\Nutgram\Nutgram;

final readonly class InterestCallback
{
    public function __construct(
        private RespondToListingInterest $respond,
    )
    {
    }

    public function accept(Nutgram $bot, string $id): void
    {
        $this->answer(
            $bot,
            fn() => $this->respond->accept((int)$id, (string)$bot->chatId()),
            'Вы поделились контактом.',
        );
    }

    public function decline(Nutgram $bot, string $id): void
    {
        $this->answer(
            $bot,
            fn() => $this->respond->decline((int)$id, (string)$bot->chatId()),
            'Контакт не отправляем.',
        );
    }

    private function answer(Nutgram $bot, callable $action, string $okText): void
    {
        try {
            $action();
        } catch (DomainException $e) {
            $bot->answerCallbackQuery(text: $e->getMessage(), show_alert: true);
            return;
        }

        $bot->answerCallbackQuery();
        $bot->editMessageReplyMarkup();
        $bot->sendMessage($okText);
    }

}
