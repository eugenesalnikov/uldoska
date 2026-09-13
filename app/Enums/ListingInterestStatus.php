<?php

namespace App\Enums;

enum ListingInterestStatus: string
{
    case Pending  = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает ответа',
            self::Accepted => 'Контакт отправлен',
            self::Declined => 'Отклонён',
        };
    }

}
