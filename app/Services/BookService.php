<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Discard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookService
{
    private const GOOGLE_BOOKS_API_URL = 'https://www.googleapis.com/books/v1/volumes';

    protected array $relations = ['images', 'genres', 'categories', 'authors'];

    /**
     * Get a paginated list of books with optional filtering and relationships.
     *
     * @param Request $request
     * @param int $perPage
     * @return mixed
     */
    public function getAllBooks(Request $request, int $perPage)
    {
        return Book::with($this->relations)
                        ->filter($request->only(['search']))
                        ->paginate($perPage);
    }

    /**
     * Create a new book entry in the database with associated relationships.
     *
     * @param array $data
     * @param Request $request
     * @return Book
     */
    public function createBook(array $data, Request $request): Book
    {
        $book = Book::create($data);

        $book->categories()->attach($request->input('categories', []));
        $book->genres()->attach($request->input('genres', []));
        $book->authors()->attach($request->input('authors', []));
        $book->publishers()->attach($request->input('publishers', []));

        return $book;
    }

    /**
     * Update an existing book's data and sync its relationships.
     *
     * @param Book $book
     * @param array $data
     * @param Request $request
     * @return Book
     */
    public function updateBook(Book $book, array $data, Request $request): Book
    {
        $book->update($data);

        $book->categories()->sync($request->input('categories', []));
        $book->genres()->sync($request->input('genres', []));
        $book->authors()->sync($request->input('authors', []));
        $book->publishers()->sync($request->input('publishers', []));

        return $book;
    }

    /**
     * Discard a book from the inventory by reducing its available copies and deleting if none remain.
     *
     * @param Book $book
     * @return JsonResponse|void
     */
    public function discardBook(Book $book)
    {
        if ($book->number_of_copies <= 0) {
            return response()->json([
                'error' => 'This book cannot be discarded as it does not exist in the inventory.',
            ], 400);
        }

        $admin = auth()->user();

        Discard::create([
            'book_id' => $book->id,
            'admin_id' => $admin->id,
        ]);

        $book->decrement('number_of_copies');

        if ($book->number_of_copies === 0) {
            $book->delete();
        }
    }

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

    /**
     * Fetch book details.
     *
     * @param $bookId
     * @return array
     */
    public function fetchBookDetails($bookId)
    {
        $response = Http::get(self::GOOGLE_BOOKS_API_URL . "/{$bookId}");
        return $response->json();
    }
}
