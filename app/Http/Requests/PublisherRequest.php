<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublisherRequest extends FormRequest
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
        $publisherId = $this->route('publisher') ? $this->route('publisher')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'established_year' => ['nullable', 'digits:4', 'integer'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('publishers', 'email')->ignore($publisherId)],
        ];

    }
}
