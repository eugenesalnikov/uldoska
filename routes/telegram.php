<?php

use App\Telegram\Commands\ContactHandler;
use App\Telegram\Commands\InterestCallback;
use App\Telegram\Commands\MyListingsCommand;
use App\Telegram\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->onCommand('start', [StartCommand::class, 'welcome']);

$bot->onCommand('start c_{token}', [StartCommand::class, 'confirm'])
    ->where('token', '.+');

$bot->onCommand('start i_{token}', [StartCommand::class, 'interest'])
    ->where('token', '.+');

$bot->onCommand('start {token}', [StartCommand::class, 'invalid'])
    ->where('token', '(?!c_|i_).+');

$bot->onCommand('my', [MyListingsCommand::class, 'handle'])
    ->description('Мои объявления');

$bot->onCallbackQueryData('interest:accept:{id}', [InterestCallback::class, 'accept'])
    ->where('id', '\d+');

$bot->onCallbackQueryData('interest:decline:{id}', [InterestCallback::class, 'decline'])
    ->where('id', '\d+');

$bot->onContact([ContactHandler::class, 'handle']);
