<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $userId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name' => [
                'required',
                'max:500',
                Rule::unique('categories', 'name')->ignore($userId)
            ],
            'description' => ['required', 'max:500'],
            'icon' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
