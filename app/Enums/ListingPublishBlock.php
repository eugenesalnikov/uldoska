<?php

namespace App\Enums;

enum ListingPublishBlock: string
{
    case Published = 'published';
    case Dead      = 'dead';
    case Status    = 'status';
    case Phone     = 'phone';
    case Photos    = 'photos';
    case Limit     = 'limit';

    public function message(): string
    {
        return match ($this) {
            self::Published => 'Объявление уже в ленте.',
            self::Dead      => 'Это объявление нельзя вернуть в ленту.',
            self::Status    => 'В ленту можно пустить только объявление с модерации или истекшее.',
            self::Phone     => 'Сначала подтвердите номер телефона в Telegram.',
            self::Photos    => 'Фото ещё обрабатываются. Попробуйте чуть позже.',
            self::Limit     => 'Можно держать не больше 5 объявлений на доске одновременно.',
        };
    }

}
