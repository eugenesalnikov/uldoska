<?php

use App\Telegram\Commands\MyListingsCommand;
use App\Telegram\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->registerCommand(StartCommand::class);
$bot->registerCommand(MyListingsCommand::class);
