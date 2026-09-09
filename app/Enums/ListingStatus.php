<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Pending   = 'pending';
    case Review    = 'review';
    case Published = 'published';
    case Expired   = 'expired';
    case Removed   = 'removed';
    case Rejected  = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает подтверждения',
            self::Review => 'На модерации',
            self::Published => 'Опубликовано',
            self::Expired => 'Истекло',
            self::Removed => 'Снято',
            self::Rejected => 'Отклонено',
        };
    }

}
