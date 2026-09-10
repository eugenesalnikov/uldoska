<?php

namespace App\Telegram\Commands;

use App\Actions\ConfirmListingAction;
use RuntimeException;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class StartCommand extends Command
{
    protected string $command = 'start {token}?';

    protected ?string $description = 'Подтверждение объявления';

    public function handle(Nutgram $bot, ?string $token = null): void
    {
        if (blank($token)) {
            $bot->sendMessage('Чтобы подтвердить объявление, перейдите по ссылке с сайта.');
            return;
        }

        try {
            $listing = app(ConfirmListingAction::class)->execute(
                $token,
                (string)$bot->chatId(),
            );
        } catch (RuntimeException $e) {
            $bot->sendMessage($e->getMessage());
            return;
        }

        $text = $listing->wasChanged()
            ? "Объявление «{$listing->title}» подтверждено.\nОно отправлено на модерацию."
            : "Объявление «{$listing->title}» уже подтверждено и находится на модерации.";

        if ($listing->hasReachedPublishedLimit()) {
            $text .= "\n\nСейчас в ленте уже 5 ваших объявлений. Новое не опубликуют, пока не снимете одно:";

            foreach ($listing->publishedListingsForTelegram() as $publishedListing) {
                $title = e($publishedListing->title);
                $url = route('listings.manage', $publishedListing->manage_token);
                $text .= "\n• <a href=\"$url\">$title</a>";
            }
        }

        $bot->sendMessage(
            text: $text,
            parse_mode: ParseMode::HTML,
            disable_web_page_preview: true,
        );
    }

}
