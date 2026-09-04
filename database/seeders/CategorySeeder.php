<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'realty' => [
                'name' => 'Недвижимость',
                'children' => [
                    'kvartiry' => 'Квартиры',
                    'komnaty' => 'Комнаты',
                    'doma' => 'Дома и дачи',
                    'uchastki' => 'Участки',
                    'garazhi' => 'Гаражи',
                    'arenda' => 'Аренда',
                ],
            ],
            'transport' => [
                'name' => 'Транспорт',
                'children' => [
                    'avto' => 'Автомобили',
                    'moto' => 'Мото',
                    'velosipedy' => 'Велосипеды',
                    'zapchasti' => 'Запчасти',
                ],
            ],
            'electronics' => [
                'name' => 'Электроника',
                'children' => [
                    'telefony' => 'Телефоны',
                    'noutbuki' => 'Ноутбуки и ПК',
                    'tv' => 'ТВ и аудио',
                    'bytovaya' => 'Бытовая техника',
                ],
            ],
            'home' => [
                'name' => 'Для дома',
                'children' => [
                    'mebel' => 'Мебель',
                    'posuda' => 'Посуда',
                    'remont' => 'Ремонт и стройка',
                    'dacha' => 'Сад и дача',
                ],
            ],
            'zhivotnye' => [
                'name' => 'Животные',
                'children' => [
                    'sobaki' => 'Собаки',
                    'koshki' => 'Кошки',
                    'drugie-zhivotnye' => 'Другие животные',
                    'tovary-zhivotnye' => 'Товары для животных',
                ],
            ],
            'detskoe' => [
                'name' => 'Детское',
                'children' => [
                    'odezhda-deti' => 'Одежда',
                    'kolyaski' => 'Коляски',
                    'igrushki' => 'Игрушки',
                    'detskaya-mebel' => 'Мебель',
                ],
            ],
            'odezhda' => [
                'name' => 'Одежда и обувь',
                'children' => [
                    'zhenskaya' => 'Женская',
                    'muzhskaya' => 'Мужская',
                    'aksessuary' => 'Аксессуары',
                ],
            ],
            'rabota' => [
                'name' => 'Работа',
                'children' => [
                    'vakansii' => 'Вакансии',
                    'rezume' => 'Резюме',
                ],
            ],
            'uslugi' => [
                'name' => 'Услуги',
                'children' => [
                    'remont-uslugi' => 'Ремонт',
                    'krasota' => 'Красота',
                    'obuchenie' => 'Обучение',
                    'prochie-uslugi' => 'Прочие услуги',
                ],
            ],
            'other' => [
                'name' => 'Другое',
                'children' => [
                    'otdam' => 'Отдам даром',
                    'prochee' => 'Прочее',
                ],
            ],
        ];

        $sort = 0;

        foreach ($tree as $slug => $item) {
            $sort++;

            $parent = Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'parent_id' => null,
                    'name' => $item['name'],
                    'sort' => $sort,
                    'is_active' => true,
                ]
            );

            $childSort = 0;

            foreach ($item['children'] as $childSlug => $childName) {
                $childSort++;

                Category::query()->updateOrCreate(
                    ['slug' => $childSlug],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'sort' => $childSort,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
