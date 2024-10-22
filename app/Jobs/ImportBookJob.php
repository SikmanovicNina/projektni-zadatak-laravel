<?php

namespace App\Jobs;

use App\Models\Author;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ImportBookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    protected array $volumeInfo;
    protected string $isbn;
    protected string $bookId;

    public function __construct(array $volumeInfo, string $isbn, string $id)
    {
        $this->volumeInfo = $volumeInfo;
        $this->isbn = $isbn;
        $this->bookId = $id;
    }

    /**
     * Handle the execution of the job to import a book into the database.
     *
     * Workflow:
     * - Retrieves the volume information, ISBN, and authors from the job properties.
     * - Uses the ISBN to either update an existing book record or create a new one in the database.
     * - For each author associated with the book, it checks if the author already exists; if not, it creates a new author record.
     * - Links the authors to the book in the database.
     *
     * @return void
     */
    public function handle(BookService $bookService)
    {
        Redis::throttle('key')->block(0)->allow(10)->every(1)->then(function () use ($bookService) {
            $authors = $this->volumeInfo['authors'] ?? [];
            $book = $this->createOrUpdateBook($this->volumeInfo, $this->isbn);
            $this->processAuthors($authors, $book);

            if ($this->bookId) {
                $response = $bookService->fetchBookDetails($this->bookId);

                if ($response) {
                    $this->handleImageLinks($response, $book);
                }
            }
        }, function () {
            return $this->release(30);
        });


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
                'number_of_pages' => $volumeInfo['pageCount'] ?? 100,
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

    /**
     * Handle the image links from the API response.
     *
     * @param array $bookData
     * @param Book $book
     * @return void
     */
    protected function handleImageLinks(array $bookData, Book $book)
    {
        $imageUrl = $bookData['volumeInfo']['imageLinks']['thumbnail'] ?? null;

        if ($imageUrl) {
            $book->images()->create([
                'image' => basename($imageUrl),
                'cover_image' => true,
            ]);
        } else {
            Log::warning("No image links found for book ID:");
        }
    }

}
