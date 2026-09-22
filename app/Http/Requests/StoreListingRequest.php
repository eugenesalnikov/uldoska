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
                )
            ],
            'district_id' => [
                'nullable',
                'integer',
                Rule::exists('districts', 'id')->where(fn($q) => $q
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                ),
            ],
            'price'       => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'body'        => ['required', 'string', 'max:4000'],
            'photo_ids'   => [
                'nullable',
                'array',
                'max:' . config('uldoska.max_attached_photos_count'),
            ],
            'photo_ids.*' => ['uuid', 'distinct'],
            'agree'       => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $price = preg_replace('/\D+/', '', (string)$this->input('price'));

        $this->merge([
            'price'       => $price === '' ? null : $price,
            'district_id' => $this->input('district_id') === '' || $this->input('district_id') === null
                ? null
                : $this->input('district_id'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'photo_ids'   => 'фото',
            'photo_ids.*' => 'фото',
            'title'       => 'заголовок',
            'body'        => 'текст',
            'category_id' => 'категория',
            'district_id' => 'район',
            'price'       => 'цена',
            'agree'       => 'согласие с правилами',
        ];
    }

    public function toData(string $ownerToken): StoreListingData
    {
        return new StoreListingData(
            districtId: $this->filled('district_id') ? $this->integer('district_id') : null,
            categoryId: $this->integer('category_id'),
            title: $this->string('title')->toString(),
            body: $this->string('body')->toString(),
            price: $this->filled('price') ? $this->integer('price') : null,
            photoUuids: $this->collect('photo_ids')->filter()->unique()->values()->all(),
            ownerToken: $ownerToken,
        );
    }

}
