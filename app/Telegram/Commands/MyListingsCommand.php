<?php

namespace App\Telegram\Commands;

use App\Enums\ListingStatus;
use App\Models\Listing;
use Illuminate\Support\Collection;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

final readonly class MyListingsCommand
{
    public function handle(Nutgram $bot): void
    {
        $listings = Listing::query()
            ->ownedByTelegram((string)$bot->chatId())
            ->latest()
            ->get();

        if ($listings->isEmpty()) {
            $bot->sendMessage('У вас пока нет объявлений.');
            return;
        }

        $bot->sendMessage(
            text: $this->format($listings),
            parse_mode: ParseMode::HTML,
            disable_web_page_preview: true,
        );
    }

    /**
     * @param Collection<Listing> $listings
     * @return string
     */
    private function format(Collection $listings): string
    {
        $sections = [
            ListingStatus::Published->value => 'В ленте',
            ListingStatus::Review->value    => 'На модерации',
            ListingStatus::Expired->value   => 'Истекли',
        ];

        $text = '';

        foreach ($sections as $status => $heading) {
            $items = $listings->filter(
                fn(Listing $listing) => $listing->status === ListingStatus::from($status)
            );

            if ($items->isEmpty()) {
                continue;
            }

            $text .= "<b>$heading</b>\n";
            /**
             * @var Listing $listing
             */
            foreach ($items as $listing) {
                $title = e($listing->title);
                $url = route('listings.manage', $listing->manage_token);
                $text .= "• <a href=\"$url\">$title</a>\n";
            }

            $text .= "\n";
        }

        return trim($text) !== ''
            ? rtrim($text . "Ссылкой для управления ни с кем не делитесь — по ней можно снять или изменить объявление.\n")
            : "Нет объявлений в ленте, на модерации или истекших.\n\nПодать свое объявление можно тут: " . route('listings.create');
    }

}
