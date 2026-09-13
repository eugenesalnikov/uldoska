<?php

use App\Telegram\Commands\MyListingsCommand;
use App\Telegram\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->onCommand('start', [StartCommand::class, 'handle']);
$bot->onCommand('start {token}', [StartCommand::class, 'handle']);
$bot->onCommand('my', [MyListingsCommand::class, 'handle'])
    ->description('Мои объявления');
