<?php

namespace App\Telegram\Commands;

use App\Actions\ConfirmListingAction;
use RuntimeException;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;

class StartCommand extends Command
{
    protected string $command = 'start {token}?';

    public function __construct(
        private readonly ConfirmListingAction $confirmListing,
    ) {}

    public function handle(Nutgram $bot, ?string $token = null): void
    {
        if (blank($token)) {
            $bot->sendMessage('Чтобы подтвердить объявление, перейдите по ссылке с сайта.');
            return;
        }

        try {
            $listing = $this->confirmListing->execute(
                $token,
                (string) $bot->chatId(),
            );
        } catch (RuntimeException $e) {
            $bot->sendMessage($e->getMessage());
            return;
        }

        $bot->sendMessage(
            $listing->wasChanged()
                ? "Объявление «{$listing->title}» подтверждено.\nОно отправлено на модерацию."
                : "Объявление «{$listing->title}» уже подтверждено и находится на модерации."
        );
    }

}
