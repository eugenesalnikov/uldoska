<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            ['name' => 'Север', 'slug' => 'sever', 'sort' => 1],
            ['name' => 'Центр', 'slug' => 'centr', 'sort' => 2],
            ['name' => 'Ближнее Засвияжье', 'slug' => 'blizhnee-zasviyazhe', 'sort' => 3],
            ['name' => 'Дальнее Засвияжье', 'slug' => 'dalnee-zasviyazhe', 'sort' => 4],
            ['name' => '4-й микрорайон', 'slug' => '4-j-mikrorajon', 'sort' => 5],
            ['name' => 'Киндяковка', 'slug' => 'kindyakovka', 'sort' => 6],
            ['name' => 'Нижняя Терраса', 'slug' => 'nizhnyaya-terrasa', 'sort' => 7],
            ['name' => 'Верхняя Терраса', 'slug' => 'verhnyaya-terrasa', 'sort' => 8],
            ['name' => 'Новый город и Промзона', 'slug' => 'novyj-gorod-i-promzona', 'sort' => 9],
            ['name' => 'Ишеевка', 'slug' => 'isheevka', 'sort' => 10],
            ['name' => 'Лаишевка', 'slug' => 'laishevka', 'sort' => 11],
        ];

        foreach ($districts as $district) {
            District::query()->updateOrCreate(
                ['slug' => $district['slug']],
                $district + ['is_active' => true]
            );
        }
    }

}
