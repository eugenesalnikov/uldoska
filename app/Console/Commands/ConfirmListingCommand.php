<?php

namespace App\Console\Commands;

use App\Actions\ConfirmListingAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;

#[Signature('listings:confirm {token} {chat=dev-chat}')]
#[Description('Command description')]
class ConfirmListingCommand extends Command
{
    public function handle(ConfirmListingAction $action): int
    {
        try {
            $listing = $action->execute(
                $this->argument('token'),
                $this->argument('chat'),
            );
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("«{$listing->title}»: {$listing->status->label()}");

        return self::SUCCESS;
    }

}
