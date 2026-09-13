<?php

namespace App\Actions;

use App\Enums\ListingInterestStatus;
use App\Events\ListingInterestRequested;
use App\Exceptions\DomainException;
use App\Models\Listing;
use App\Models\ListingInterest;

final readonly class SendInterestToListing
{
    /**
     * @throws DomainException
     */
    public function execute(
        string  $publicCode,
        string  $chatId,
        ?string $username,
        ?string $name
    ): ListingInterest
    {
        $listing = Listing::query()
            ->where('public_code', $publicCode)
            ->first();

        if (!$listing) {
            throw new DomainException('Объявление не найдено.');
        }

        if ((string)$listing->telegram_chat_id === $chatId) {
            throw new DomainException('Это ваше объявление.');
        }

        if (blank($listing->telegram_chat_id)) {
            throw new DomainException('Автор объявления ещё не подключён к боту.');
        }

        $interest = ListingInterest::query()->firstOrCreate(
            [
                'listing_id'         => $listing->id,
                'interested_chat_id' => $chatId,
            ],
            [
                'interested_username' => $username,
                'interested_name'     => $name,
                'status'              => ListingInterestStatus::Pending,
            ],
        );

        if (!$interest->wasRecentlyCreated) {
            throw new DomainException(
                match ($interest->status) {
                    ListingInterestStatus::Accepted => 'Автор уже получил ваш запрос и ответил.',
                    ListingInterestStatus::Declined => 'Автор уже отклонил этот запрос.',
                    ListingInterestStatus::Pending => 'Запрос уже отправлен автору. Дождитесь ответа.',
                }
            );
        }

        ListingInterestRequested::dispatch($interest);

        return $interest;
    }

}
