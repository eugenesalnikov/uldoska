<?php

namespace App\Console\Commands;

use App\Enums\ListingStatus;
use App\Models\Listing;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('listings:expire')]
#[Description('Пометить просроченные объявления как expired')]
class ExpireListingsCommand extends Command
{
    public function handle(): int
    {
        $count = Listing::query()
            ->where('status', ListingStatus::Published)
            ->whereNotNull('expires_at')
            ->whereDate('listings.expires_at', '<=', now())
            ->update(['status' => ListingStatus::Expired]);

        $this->info("Обновлено объявлений: $count");

        return self::SUCCESS;
    }

}
