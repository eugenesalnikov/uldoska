<?php

namespace App\Listeners;

use App\Events\ListingSubmittedForReview;
use Exception;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class NotifyAuthorListingSubmittedForReview
{
    public function handle(ListingSubmittedForReview $event): void
    {
        $listing = $event->listing;
        $chatId = $listing->telegram_chat_id;

        if (blank($chatId)) {
            return;
        }

        $managementLink = route('listings.manage', $listing);

        $text = implode("\n", [
            "Объявление «{$listing->title}» подтверждено. Оно отправлено на модерацию.",
            "",
            "Ссылка на управление объявлением: $managementLink",
            "Ссылкой для управления ни с кем не делитесь — по ней можно снять или изменить объявление."
        ]);

        if ($listing->hasReachedPublishedLimit()) {
            $text .= "\n\nСейчас в ленте уже 5 ваших объявлений. Новое не опубликуют, пока не снимете одно:";

            foreach ($listing->publishedListingsForTelegram() as $publishedListing) {
                $title = e($publishedListing->title);
                $url = route('listings.manage', $publishedListing->manage_token);
                $text .= "\n• <a href=\"$url\">$title</a>";
            }
        }

        try {
            Telegram::sendMessage(
                text: $text,
                chat_id: $chatId,
                parse_mode: ParseMode::HTML,
                disable_web_page_preview: true,
            );
        } catch (Exception $e) {
            report($e);
        }
    }

}
