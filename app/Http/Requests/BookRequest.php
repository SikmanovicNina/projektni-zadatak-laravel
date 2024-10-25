<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bookId = $this->route('book') ? $this->route('book')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'number_of_pages' => ['required', 'integer', 'min:1', 'max:20000'],
            'number_of_copies' => ['required', 'integer', 'min:1'],
            'isbn' => [
                'required',
                'string',
                Rule::unique('books', 'isbn')->ignore($bookId),
                'max:13'
            ],
            'language' => ['required', 'string', 'max:50'],
            'binding' => ['required', 'string', Rule::in(Book::BINDINGS)],
            'script' => ['required', 'string', Rule::in(Book::SCRIPTS)],
            'dimensions' => ['required', 'string', Rule::in(Book::DIMENSIONS)],
        ];

    }
}
