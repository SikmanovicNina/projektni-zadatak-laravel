<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Discard;
use Illuminate\Support\Facades\Http;

class BookService
{
    private const GOOGLE_BOOKS_API_URL = 'https://www.googleapis.com/books/v1/volumes';

    protected array $relations = ['images', 'genres', 'categories', 'authors'];

    /**
     * Get a paginated list of books with optional filtering and relationships.
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAllBooks(array $filters, int $perPage)
    {
        return Book::with($this->relations)->filter($filters)->paginate($perPage);
    }

    /**
     * Create a new book entry in the database with associated relationships.
     *
     * @param array $data
     * @return Book
     */
    public function createBook(array $data)
    {
        $book = Book::create($data);
        $this->syncBookRelations($book, $data);

        return $book;
    }

    /**
     * Update an existing book's data and sync its relationships.
     *
     * @param Book $book
     * @param array $data
     * @return Book
     */
    public function updateBook(Book $book, array $data)
    {
        $book->update($data);
        $this->syncBookRelations($book, $data);

        return $book;
    }

    /**
     * Sync book relationships (publishers, categories, genres, authors).
     *
     * @param Book $book
     * @param array $data
     * @return void
     */
    private function syncBookRelations(Book $book, array $data)
    {
        $book->publishers()->sync($data['publishers'] ?? []);
        $book->categories()->sync($data['categories'] ?? []);
        $book->genres()->sync($data['genres'] ?? []);
        $book->authors()->sync($data['authors'] ?? []);
    }

    /**
     * Discard a book from the inventory by reducing its available copies and deleting if none remain.
     *
     * @param Book $book
     * @return void
     */
    public function discardBook(Book $book)
    {
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

    public function deleteBook(Book $book)
    {
        $book->delete();
    }
}
