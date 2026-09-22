<?php

namespace App\Enums;

enum ListingRejectionReason: string
{
    case NotLocal    = 'not_local';
    case Reseller    = 'reseller';
    case PoorContent = 'poor_content';
    case Duplicate   = 'duplicate';
    case Other       = 'other';

    public function label(): string
    {
        return match ($this) {
            self::NotLocal    => 'Объявление не про Ульяновск',
            self::Reseller    => 'Похоже на магазин или опт',
            self::PoorContent => 'Недостаточно данных или чужие фото',
            self::Duplicate   => 'Повтор уже существующего объявления',
            self::Other       => 'Другая причина',
        };
    }

}
