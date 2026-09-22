<?php

namespace App\Listeners;

use App\Events\ListingPhoneVerified;
use App\Models\Listing;
use Exception;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

final readonly class NotifyAuthorListingPhoneVerified
{
    public function handle(ListingPhoneVerified $event): void
    {
        $listing = Listing::query()->find($event->listingId);

        if ($listing === null) {
            return;
        }

        $chatId = $listing->telegram_chat_id;

        if (blank($chatId)) {
            return;
        }

        $managementLink = route('listings.manage', [
            'listing' => $listing,
            'key'     => $listing->manage_token,
        ]);

        $text = implode("\n", [
            "По объявлению «{$listing->title}» телефон подтвержден.",
            "",
            "Ссылка на управление объявлением: $managementLink",
            "Ссылкой для управления ни с кем не делитесь – по ней можно снять или изменить объявление.",
            "Ссылка является одноразовой. Она обновится после первого перехода по ней. Новая ссылка будет тут – /my",
        ]);

        if ($listing->hasReachedPublishedLimit()) {
            $text .= "\n\nСейчас в ленте уже 5 ваших объявлений. Новое не опубликуют, пока не снимете одно:";

            foreach ($listing->publishedListingsForTelegram() as $publishedListing) {
                $title = e($publishedListing->title);
                $url = route('listings.manage', [
                    'listing' => $publishedListing,
                    'key'     => $publishedListing->manage_token,
                ]);
                $text .= "\n• <a href=\"$url\">$title</a>";
            }
        }

        try {
            Telegram::sendMessage(
                text: $text,
                chat_id: $chatId,
                parse_mode: ParseMode::HTML,
                disable_web_page_preview: true,
                reply_markup: ReplyKeyboardRemove::make(true),
            );
        } catch (Exception $e) {
            report($e);
        }
    }

}
