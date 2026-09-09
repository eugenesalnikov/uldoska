<?php

use App\Actions\ConfirmListingAction;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->onCommand('start {token}', function (Nutgram $bot, string $token) {
    if (blank($token)) {
        $bot->sendMessage('Чтобы подтвердить объявление, перейдите по ссылке с сайта.');
        return;
    }

    try {
        $listing = app(ConfirmListingAction::class)->execute(
            $token,
            (string) $bot->chatId()
        );

        if ($listing->wasChanged()) {
            $bot->sendMessage(
                "Объявление «{$listing->title}» подтверждено.\nОно отправлено на модерацию."
            );
            return;
        }

        $bot->sendMessage(
            "Объявление «{$listing->title}» уже подтверждено и находится на модерации."
        );
    } catch (RuntimeException $e) {
        $bot->sendMessage($e->getMessage());
    }
});

$bot->onCommand('start', function (Nutgram $bot) {
    $bot->sendMessage('Откройте ссылку подтверждения на сайте объявления.');
});
