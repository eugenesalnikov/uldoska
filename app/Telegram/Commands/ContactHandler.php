<?php

namespace App\Telegram\Commands;

use App\Actions\VerifyPhoneAction;
use App\Exceptions\DomainException;
use SergiX44\Nutgram\Nutgram;

final readonly class ContactHandler
{
    public function __construct(
        private VerifyPhoneAction $verifyPhoneAction,
    )
    {
    }

    public function handle(Nutgram $bot): void
    {
        $token = $bot->getUserData('verification_token');

        if (blank($token)) {
            $bot->sendMessage(
                'Нет объявления, ожидающего подтверждения.'
            );

            return;
        }

        $contact = $bot->message()->contact;

        if (!$contact) {
            return;
        }

        if (
            $contact->user_id === null
            || (string)$contact->user_id !== (string)$bot->userId()
        ) {
            $bot->sendMessage('Можно подтвердить только свой номер телефона.');
            return;
        }

        try {
            $this->verifyPhoneAction->execute(
                manageToken: (string)$token,
                chatId: (string)$bot->chatId(),
                phone: $contact->phone_number,
            );
        } catch (DomainException $e) {
            $bot->sendMessage($e->getMessage());
            return;
        }

        $bot->deleteUserData('verification_token');
    }

}