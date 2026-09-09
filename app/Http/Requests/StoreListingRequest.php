<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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
            'phone'       => ['required', 'regex:/^\+7\d{10}$/'],
            'agree'       => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $price = preg_replace('/\D+/', '', (string)$this->input('price'));

        $phone = preg_replace('/[^\d+]/', '', (string)$this->input('phone'));

        if (str_starts_with($phone, '8') && strlen($phone) === 11) {
            $phone = '+7' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') && strlen($phone) === 11) {
            $phone = '+' . $phone;
        } elseif (str_starts_with($phone, '9') && strlen($phone) === 10) {
            $phone = '+7' . $phone;
        }

        $this->merge([
            'price' => $price === '' ? null : $price,
            'phone' => $phone,
        ]);
    }

    public function attributes(): array
    {
        return [
            'photos'      => 'фото',
            'photos.*'    => 'фото',
            'title'       => 'заголовок',
            'body'        => 'текст',
            'phone'       => 'телефон',
            'category_id' => 'категория',
            'district_id' => 'район',
            'price'       => 'цена',
            'agree'       => 'согласие с правилами',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Укажите телефон в формате +7XXXXXXXXXX.',
        ];
    }

}
