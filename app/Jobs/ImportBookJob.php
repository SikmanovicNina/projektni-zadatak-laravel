<?php

namespace App\Jobs;

use App\Models\Author;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\LimiterTimeoutException;
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
     * Handles the processing of a single book by creating or updating the book record,
     * processing its authors, and fetching additional book details if available.
     *
     * This function:
     * 1. Uses Redis throttling to limit the number of times this job can run, allowing
     *    a maximum of 10 executions every 1 second, and immediately blocks if the rate limit is hit.
     * 2. Inside the throttled block:
     *    - Retrieves the authors from the provided `volumeInfo`.
     *    - Calls the `createOrUpdateBook()` method to create or update the book record in the database
     *      using the provided `volumeInfo` and `isbn`.
     *    - Calls the `processAuthors()` method to associate the authors with the book.
     *    - If a `bookId` is available, it fetches additional book details from the external service
     *      using `BookService::fetchBookDetails()`.
     *    - If the API response is successful, it processes and handles image links associated with the book.
     * 3. If the Redis throttle fails (e.g., due to rate limits), the job is released and retried after 30 seconds.
     *
     * @param BookService $bookService The service used to fetch additional book details.
     * @return void
     * @throws LimiterTimeoutException
     */

    public function handle(BookService $bookService)
    {
        Redis::throttle('api-fetch-book-details')->block(0)->allow(10)->every(1)->then(function () use ($bookService) {
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
                'number_of_pages' => $volumeInfo['pageCount'] ?? null,
                'number_of_copies' =>  null,
                'language' => $volumeInfo['language'] ?? null,
                'binding' => null,
                'script' => null,
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
                    'last_name' => $nameParts[1] ?? null,
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
