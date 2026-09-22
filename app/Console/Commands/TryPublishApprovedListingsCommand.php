<?php

namespace App\Console\Commands;

use App\Actions\Listing\PublishListingAction;
use App\Enums\ListingPhotosStatus;
use App\Enums\ListingStatus;
use App\Models\Listing;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('listings:try-publish')]
#[Description('Публикует застрявшие одобренные объявления, если все условия уже выполнены')]
class TryPublishApprovedListingsCommand extends Command
{
    public function handle(PublishListingAction $action): int
    {
        $listings = Listing::query()
            ->where('status', ListingStatus::Approved)
            ->whereNotNull('phone')
            ->where('photos_status', ListingPhotosStatus::Ready)
            ->orderBy('id')
            ->get();

        $listings->each(fn(Listing $listing) => $action->try($listing));

        $this->info("Проверено объявлений: {$listings->count()}");

        return self::SUCCESS;
    }

}
