<?php

namespace App\Http\Requests;

use App\Data\UploadedPhotoData;
use App\Enums\PendingPhotoStatus;
use App\Models\PendingPhoto;
use App\Services\PhotoOwnerToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePendingPhotoRequest extends FormRequest
{
    public function __construct(
        private readonly PhotoOwnerToken $ownerToken,
    )
    {
        parent::__construct();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filepond' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:' . config('uldoska.max_attached_photo_size'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'filepond.required' => 'Выберите фото.',
            'filepond.image'    => 'Можно загружать только изображения.',
            'filepond.mimes'    => 'Допустимы jpg, png, webp и gif.',
            'filepond.max'      => 'Файл не должен быть больше 5 МБ.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $count = PendingPhoto::query()
                    ->where('owner_token', $this->ownerToken->get())
                    ->where('status', PendingPhotoStatus::Uploaded)
                    ->count();

                $max = config('uldoska.max_attached_photos_count', 8);

                if ($count >= $max) {
                    $validator->errors()->add(
                        'filepond',
                        "Можно загрузить не больше $max фото.",
                    );
                }
            },
        ];
    }

    public function toData(): UploadedPhotoData
    {
        $file = $this->file('filepond');

        return new UploadedPhotoData(
            path: $file->store('tmp/photos', 'local'),
            originalName: $file->getClientOriginalName(),
            mime: (string)$file->getMimeType(),
            size: $file->getSize(),
        );
    }

}
