<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'sometimes',
                'min:3',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($this->user()->id),
            ],
        ];
    }
}
