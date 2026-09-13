<?php

namespace App\Http\Requests;

use App\Data\StoreListingData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:80'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn($q) => $q
                    ->whereNotNull('parent_id')
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                )],
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where(fn($q) => $q
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                ),
            ],
            'price'       => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'body'        => ['required', 'string', 'max:4000'],
            'photos'      => ['nullable', 'array', 'max:8'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'agree'       => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $price = preg_replace('/\D+/', '', (string)$this->input('price'));

        $this->merge([
            'price' => $price === '' ? null : $price,
        ]);
    }

    public function attributes(): array
    {
        return [
            'photos'      => 'фото',
            'photos.*'    => 'фото',
            'title'       => 'заголовок',
            'body'        => 'текст',
            'category_id' => 'категория',
            'district_id' => 'район',
            'price'       => 'цена',
            'agree'       => 'согласие с правилами',
        ];
    }

    public function toData(): StoreListingData
    {
        return new StoreListingData(
            districtId: $this->integer('district_id'),
            categoryId: $this->integer('category_id'),
            title: $this->string('title')->toString(),
            body: $this->string('body')->toString(),
            price: $this->filled('price') ? $this->integer('price') : null,
            photoPaths: $this->file('photos', []),
        );
    }

}
