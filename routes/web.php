<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/listings', function () {
    return view('listings.index', []);
})->name('listings.index');

Route::get('/listings/{id}', function () {
    return view('listings.show', [
        'listing' => (object) [
            'title' => 'Коляска',
            'category_name' => 'Детское',
            'district_name' => 'Киндяковка',
            'published_at' => now()->subMinutes(45),
            'body' => "Задача организации, в особенности же дальнейшее развитие различных форм деятельности обеспечивает широкому кругу (специалистов) участие в формировании форм развития. Товарищи! дальнейшее развитие различных форм деятельности играет важную роль в формировании модели развития. Товарищи! реализация намеченных плановых заданий играет важную роль в формировании систем массового участия.
            Разнообразный и богатый опыт новая модель организационной деятельности требуют от нас анализа позиций, занимаемых участниками в отношении поставленных задач. ",
            'photos' => [],
            'price_label' => '12 000 ₽',
            'phone' => '+7 900 000-00-00',
        ],
    ]);
})->name('listings.show');


Route::view('/submit', 'listings.create', [
    'categories' => collect([
        (object)['id' => 1, 'name' => 'Вещи'],
        (object)['id' => 2, 'name' => 'Жильё'],
        (object)['id' => 3, 'name' => 'Услуги'],
    ]),
    'districts'  => collect([
        (object)['id' => 1, 'name' => 'Север'],
        (object)['id' => 2, 'name' => 'Центр'],
        (object)['id' => 3, 'name' => 'Ближнее Засвияжье'],
        (object)['id' => 4, 'name' => 'Дальнее Засвияжье'],
        (object)['id' => 5, 'name' => '4-й микрорайон'],
        (object)['id' => 6, 'name' => 'Киндяковка'],
        (object)['id' => 7, 'name' => 'Нижняя терраса'],
        (object)['id' => 8, 'name' => 'Верхняя терраса'],
        (object)['id' => 9, 'name' => 'Новый город и промзона'],
    ]),
])->name('listings.create');

Route::post('/submit', fn() => redirect()->route('listings.create'))
    ->name('listings.store');


Route::view('/rules', 'pages.rules')->name('pages.rules');
Route::view('/about', 'pages.about')->name('pages.about');
