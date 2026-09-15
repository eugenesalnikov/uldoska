<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\District;
use App\Models\Listing;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;

#[Signature('sitemap:generate')]
#[Description('Сгенерировать sitemap.xml')]
class GenerateSitemap extends Command
{
    public function handle(): void
    {
        $sitemap = Sitemap::create()
            ->add(route('home'))
            ->add(route('district.all'))
            ->add(route('listings.index'));

        foreach (District::all() as $district) {
            if (Listing::query()->published()->where('district_id', $district->id)->exists()) {
                $sitemap->add(route('home.district', $district));
                $sitemap->add(route('listings.district', $district));
            }
        }

        foreach (Category::all() as $category) {
            if (Listing::query()->published()->where('category_id', $category->id)->exists()) {
                $sitemap->add(route('listings.category', $category));
            }
        }

        foreach (District::all() as $district) {
            foreach (Category::all() as $category) {
                if (
                    Listing::query()
                        ->published()
                        ->where('district_id', $district->id)
                        ->where('category_id', $category->id)
                        ->exists()
                ) {
                    $sitemap->add(route('listings.district.category', [
                        'district' => $district,
                        'category' => $category,
                    ]));
                }
            }
        }

        Listing::query()->published()->each(function (Listing $listing) use ($sitemap) {
            $sitemap->add(route('listings.show', $listing));
        });

        $sitemap->writeToFile(storage_path('app/sitemap.xml'));
    }

}
