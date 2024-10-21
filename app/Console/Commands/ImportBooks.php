<?php

namespace App\Console\Commands;

use App\Jobs\ImportBookJob;
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
     * - Generates a random letter (A-Z) to fetch books based on the letter.
     * - Calls the BookService to fetch books data from the Google Books API using the generated letter.
     * - Checks if there are valid items in the API response.
     * - For each book item:
     *   - Extracts the volume information, including the ISBN and authors.
     *   - If either ISBN or authors is missing, a warning is issued and the book is skipped.
     *   - If valid data is present, a job (ImportBookJob) is dispatched to handle the import of the book asynchronously.
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
                $bookId = $bookData['id'] ?? null;

                if (!$isbn || !$authors || !$bookId) {
                    $this->warn('Incomplete data, skipping this book.');
                    continue;
                }

                /*for ($i = 0; $i < 200; $i++) {
                    ImportBookJob::dispatch($volumeInfo, $isbn, $bookId);
                } */

                ImportBookJob::dispatch($volumeInfo, $isbn, $bookId);
            }
        } else {
            $this->warn('No valid items found in the API response.');
        }
    }
}
