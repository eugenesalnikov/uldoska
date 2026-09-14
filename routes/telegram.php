<?php

use App\Telegram\Commands\ContactHandler;
use App\Telegram\Commands\InterestCallback;
use App\Telegram\Commands\MyListingsCommand;
use App\Telegram\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->onCommand('start', [StartCommand::class, 'welcome'])
    ->throttle(10, key: 'cmd.start.welcome');

$bot->onCommand('start c_{token}', [StartCommand::class, 'confirm'])
    ->where('token', '.+')
    ->throttle(10, key: 'cmd.start.confirm');

$bot->onCommand('start i_{token}', [StartCommand::class, 'interest'])
    ->where('token', '.+')
    ->throttle(10, key: 'cmd.start.interest');

$bot->onCommand('start {token}', [StartCommand::class, 'invalid'])
    ->where('token', '(?!c_|i_).+')
    ->throttle(10, key: 'cmd.start.invalid');

$bot->onCommand('my', [MyListingsCommand::class, 'handle'])
    ->description('Мои объявления')
    ->throttle(10, key: 'cmd.my');

$bot->onCallbackQueryData('interest:accept:{id}', [InterestCallback::class, 'accept'])
    ->where('id', '\d+')
    ->throttle(10, key: 'cmd.interest.accept');

$bot->onCallbackQueryData('interest:decline:{id}', [InterestCallback::class, 'decline'])
    ->where('id', '\d+')
    ->throttle(10, key: 'cmd.interest.decline');

$bot->onContact([ContactHandler::class, 'handle'])
    ->throttle(10, key: 'cmd.contact.handle');
