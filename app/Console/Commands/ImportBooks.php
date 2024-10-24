<?php

namespace App\Console\Commands;

use App\Jobs\ImportBookJob;
use App\Mail\BooksFetchedMail;
use App\Services\BookService;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
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
     * Handle the book fetching and importing process.
     *
     * This function:
     * 1. Generates a random letter between A and Z.
     * 2. Fetches books from an external API using the random letter as a query.
     * 3. Iterates through the fetched books, validates the necessary data (ISBN, authors, book ID),
     *    and skips any books with incomplete information.
     * 4. For each valid book, it creates a job to import the book data and adds it to a batch.
     * 5. If there are valid jobs, it dispatches the batch for processing.
     * 6. Once the batch is completed successfully, an email is sent to notify the user.
     * 7. If an error occurs during the batch process, the error is handled in the `catch` block
     *
     * Key components:
     * - `$randomLetter`: A randomly generated letter used to query the book API.
     * - `$booksData`: Holds the response from the external API containing the book data.
     * - `$jobs`: An array of jobs to import books that contain valid data.
     * - `Bus::batch()`: Executes a batch of jobs for importing the books.
     * - `Mail::to()`: Sends an email notification when the batch of jobs has been successfully processed.
     *
     * @return void
     * @throws Throwable
     */
    public function handle()
    {
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
                        if ($email = optional(Auth::user())->email) {
                            Mail::to($email)->send(new BooksFetchedMail());
                        }
                    })
                    ->catch(function (Batch $batch, Throwable $e) {
                        Log::error('Batch failed: ' . $e->getMessage(), [
                            'batch_id' => $batch->id,
                            'failed_jobs' => $batch->failedJobs,
                        ]);
                    })
                    ->dispatch();
            }
        } else {
            $this->warn('No valid items found in the API response.');
        }
    }
}
