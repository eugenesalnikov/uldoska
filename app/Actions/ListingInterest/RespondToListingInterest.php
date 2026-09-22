<?php

namespace App\Actions\ListingInterest;

use App\Enums\ListingInterestStatus;
use App\Events\ListingInterestAccepted;
use App\Events\ListingInterestDeclined;
use App\Exceptions\DomainException;
use App\Models\ListingInterest;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RespondToListingInterest
{
    public function accept(
        int     $interestId,
        string  $authorChatId,
        string  $authorUsername,
        ?string $authorName,
    ): ListingInterest
    {
        return $this->finalize(
            $interestId,
            $authorChatId,
            function (ListingInterest $interest) use ($authorUsername, $authorName) {
                $interest->update([
                    'status' => ListingInterestStatus::Accepted,
                ]);

                ListingInterestAccepted::dispatch(
                    $interest,
                    $authorUsername,
                    $authorName,
                );
            },
        );
    }

    public function decline(int $interestId, string $authorChatId): ListingInterest
    {
        return $this->finalize(
            $interestId,
            $authorChatId,
            function (ListingInterest $interest) {
                $interest->update([
                    'status' => ListingInterestStatus::Declined,
                ]);

                ListingInterestDeclined::dispatch($interest);
            },
        );
    }

    /**
     * @throws Throwable
     */
    private function finalize(
        int     $interestId,
        string  $authorChatId,
        Closure $callback,
    ): ListingInterest
    {
        return DB::transaction(function () use ($interestId, $authorChatId, $callback) {
            $interest = ListingInterest::query()
                ->with('listing')
                ->lockForUpdate()
                ->find($interestId);

            if (!$interest || (string)$interest->listing->telegram_chat_id !== $authorChatId) {
                throw new DomainException('Запрос не найден.');
            }

            if ($interest->status !== ListingInterestStatus::Pending) {
                throw new DomainException(
                    match ($interest->status) {
                        ListingInterestStatus::Accepted, ListingInterestStatus::Declined => 'По этому запросу уже дан ответ.',
                        ListingInterestStatus::Cancelled => 'Объявление снято, запрос больше не актуален.',
                        ListingInterestStatus::Expired => 'Автор не ответил, запрос больше не актуален.',
                        default => 'Запрос уже закрыт.',
                    }
                );
            }

            if (!$interest->listing->isPublished()) {
                throw new DomainException('Объявление уже недоступно.');
            }

            $callback($interest);

            return $interest;
        });
    }

}
