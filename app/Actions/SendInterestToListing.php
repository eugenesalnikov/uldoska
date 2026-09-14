<?php

namespace App\Actions;

use App\Enums\ListingInterestStatus;
use App\Events\ListingInterestRequested;
use App\Exceptions\DomainException;
use App\Models\Listing;
use App\Models\ListingInterest;
use Illuminate\Database\UniqueConstraintViolationException;

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
            ->published()
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

        $pendingLimit = config('uldoska.max_pending_interests', 10);

        $pendingCount = ListingInterest::query()
            ->where('interested_chat_id', $chatId)
            ->where('status', ListingInterestStatus::Pending)
            ->count();

        if ($pendingCount >= $pendingLimit) {
            throw new DomainException(
                'Слишком много ожидающих запросов. Дождитесь ответа авторов.'
            );
        }

        try {
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
        } catch (UniqueConstraintViolationException) {
            $interest = ListingInterest::query()
                ->where('listing_id', $listing->id)
                ->where('interested_chat_id', $chatId)
                ->firstOrFail();
        }

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
