<?php

namespace App\Enums;

enum ListingPhotosStatus: string
{
    case Pending = 'pending';
    case Ready   = 'ready';
    case Failed  = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'В очереди',
            self::Ready   => 'Готово',
            self::Failed  => 'Ошибка',
        };
    }

}
