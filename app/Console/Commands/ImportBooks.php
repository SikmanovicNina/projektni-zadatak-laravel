<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Console\Command;

class ImportBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import random books from Google Books API';

    /**
     * Execute the console command.
     */
    public function __construct(protected BookService $bookService)
    {
        parent::__construct();
    }

    /**
     * Handle the execution of the command to import books.
     *
     * Workflow:
     * - Generates a random letter.
     * - Fetches books data from Google Books API.
     * - For each book, it extracts volume information, including the ISBN.
     * - If a valid ISBN is found, it creates or updates the book in the database.
     * - If authors are present, it processes each author and links them to the book.
     *
     * @return void
     */
    public function handle()
    {
        $randomLetter = chr(rand(65, 90));

        $booksData = $this->bookService->fetchBooksByQuery($randomLetter);

        if (isset($booksData['items'])) {
            foreach ($booksData['items'] as $bookData) {

                $volumeInfo = $bookData['volumeInfo'];
                $isbn = $volumeInfo['industryIdentifiers'][0]['identifier'] ?? null;
                $authors = $volumeInfo['authors'] ?? null;

                if (!$isbn || !$authors) {
                    $this->warn('Incomplete data, skipping this book.');
                    continue;
                }

                $book = $this->createOrUpdateBook($volumeInfo, $isbn);
                $this->processAuthors($authors, $book);
            }
        } else {
            $this->warn('No valid items found in the API response.');
        }
    }

    /**
     * Create or update a book record in the database.
     *
     * @param array $volumeInfo The volume information of the book from the Google Books API response.
     * @param string $isbn
     * @return Book
     */
    private function createOrUpdateBook($volumeInfo, $isbn)
    {
        return Book::updateOrCreate(
            [
                'isbn' => $isbn,
            ],
            [
                'name' => $volumeInfo['title'] ?? 'Unknown Title',
                'description' => $volumeInfo['description'] ?? null,
                'number_of_pages' => $volumeInfo['pageCount'] ?? null,
                'number_of_copies' => 3,
                'language' => $volumeInfo['language'] ?? null,
                'binding' => 'Paperback',
                'script' => 'Latin',
            ]
        );
    }


    /**
     * Process and attach authors to a book.
     *
     * @param array $authors
     * @param Book $book
     * @return void
     */
    private function processAuthors(array $authors, Book $book)
    {
        foreach ($authors as $authorName) {
            $nameParts = explode(' ', trim($authorName));
            $author = Author::firstOrCreate(
                [
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? '',
                ]
            );

            $book->authors()->attach($author->id);
        }
    }
}
