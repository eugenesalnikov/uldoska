<?php

namespace App\Http\Requests;

use App\Enums\ListingRejectionReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RejectListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rejection_reason'  => ['required', Rule::enum(ListingRejectionReason::class)],
            'rejection_comment' => ['nullable', 'string', 'max:500'],
        ];
    }

}
