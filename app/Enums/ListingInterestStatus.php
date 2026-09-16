<?php

namespace App\Enums;

enum ListingInterestStatus: string
{
    case Pending   = 'pending';
    case Accepted  = 'accepted';
    case Declined  = 'declined';
    case Expired   = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает ответа',
            self::Accepted => 'Контакт отправлен',
            self::Declined => 'Отклонён',
            self::Expired => 'Ожидание истекло',
            self::Cancelled => 'Отменён',
        };
    }

}
