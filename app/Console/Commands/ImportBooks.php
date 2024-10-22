<?php

namespace App\Console\Commands;

use App\Jobs\ImportBookJob;
use App\Mail\BooksFetchedMail;
use App\Services\BookService;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
        $this->info('Novi pocetak');
        $randomLetter = chr(rand(65, 90));

        $booksData = $this->bookService->fetchBooksByQuery($randomLetter);

        $jobs = [];

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

                $jobs[] = new ImportBookJob($volumeInfo, $isbn, $bookId);
            }

            if (!empty($jobs)) {
                Bus::batch($jobs)
                    ->then(function (Batch $batch) {
                        Mail::to("testing@gmail.me")->send(new BooksFetchedMail());
                    })
                    ->catch(function (Batch $batch, Throwable $e) {
                        // Error handling callback
                    })
                    ->dispatch();
            }
        } else {
            $this->warn('No valid items found in the API response.');
        }
    }
}
