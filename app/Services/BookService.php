<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BookService
{
    private const GOOGLE_BOOKS_API_URL = 'https://www.googleapis.com/books/v1/volumes';

    /**
     * Fetch books by random title or query.
     *
     * @param string $query
     * @return array
     */
    public function fetchBooksByQuery($query = '')
    {
        $response = Http::get(
            self::GOOGLE_BOOKS_API_URL,
            ['q' => $query]
        );

        return $response->json();
    }
}
