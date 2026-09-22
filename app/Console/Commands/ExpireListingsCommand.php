<?php

namespace App\Console\Commands;

use App\Actions\Listing\ExpireListingsAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('listings:expire')]
#[Description('Пометить просроченные объявления как expired')]
class ExpireListingsCommand extends Command
{
    public function handle(ExpireListingsAction $action): int
    {
        $listings = $action->execute();

        $this->info("Обновлено объявлений: {$listings->count()}");

        return self::SUCCESS;
    }

}
