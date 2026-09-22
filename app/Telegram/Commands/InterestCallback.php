<?php

namespace App\Telegram\Commands;

use App\Actions\ListingInterest\RespondToListingInterest;
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
        $username = $bot->user()?->username;

        if (blank($username)) {
            $bot->answerCallbackQuery(
                text: 'Сначала заведите username (имя пользователя) в настройках Telegram и нажмите ещё раз.',
                show_alert: true,
            );
            return;
        }

        $this->answer(
            $bot,
            fn() => $this->respond->accept(
                interestId: (int)$id,
                authorChatId: (string)$bot->chatId(),
                authorUsername: $username,
                authorName: trim(($bot->user()?->first_name ?? '') . ' ' . ($bot->user()?->last_name ?? '')) ?: null,
            ),
            'Вы поделились своим Telegram.',
        );
    }

    public function decline(Nutgram $bot, string $id): void
    {
        $this->answer(
            $bot,
            fn() => $this->respond->decline((int)$id, (string)$bot->chatId()),
            'Вы не поделились своим Telegram.',
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
