<?php

namespace App\Telegram\Commands;

use App\Actions\ConfirmListingAction;
use App\Exceptions\DomainException;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class StartCommand extends Command
{
    protected string $command = 'start {token}?';

    protected ?string $description = null;

    public function handle(Nutgram $bot, ?string $token = null): void
    {
        if (blank($token)) {
            $bot->sendMessage('Чтобы подтвердить объявление, перейдите по ссылке с сайта.');
            return;
        }

        try {
            $listing = app(ConfirmListingAction::class)->execute(
                $token,
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

}
