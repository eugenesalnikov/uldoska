<?php

namespace App\Actions;

use App\Enums\ListingInterestStatus;
use App\Events\ListingInterestAccepted;
use App\Events\ListingInterestDeclined;
use App\Exceptions\DomainException;
use App\Models\ListingInterest;
use Illuminate\Support\Facades\DB;

final readonly class RespondToListingInterest
{
    public function accept(int $interestId, string $authorChatId): ListingInterest
    {
        return $this->respond(
            $interestId,
            $authorChatId,
            ListingInterestStatus::Accepted,
        );
    }

    public function decline(int $interestId, string $authorChatId): ListingInterest
    {
        return $this->respond(
            $interestId,
            $authorChatId,
            ListingInterestStatus::Declined,
        );
    }

    private function respond(
        int                   $interestId,
        string                $authorChatId,
        ListingInterestStatus $status,
    ): ListingInterest
    {
        return DB::transaction(function () use ($interestId, $authorChatId, $status) {
            $interest = ListingInterest::query()
                ->with('listing')
                ->lockForUpdate()
                ->find($interestId);

            if (!$interest || (string)$interest->listing->telegram_chat_id !== $authorChatId) {
                throw new DomainException('Запрос не найден.');
            }

            if ($interest->status !== ListingInterestStatus::Pending) {
                throw new DomainException('По этому запросу уже дан ответ.');
            }

            $interest->update(['status' => $status]);

            match ($status) {
                ListingInterestStatus::Accepted => ListingInterestAccepted::dispatch($interest),
                ListingInterestStatus::Declined => ListingInterestDeclined::dispatch($interest),
                default => throw new DomainException('Некорректный ответ на запрос.'),
            };

            return $interest;
        });
    }

}
