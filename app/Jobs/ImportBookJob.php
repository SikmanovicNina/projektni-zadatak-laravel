<?php

namespace App\Jobs;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportBookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected array $volumeInfo;
    protected string $isbn;

    public function __construct(array $volumeInfo, string $isbn)
    {
        $this->volumeInfo = $volumeInfo;
        $this->isbn = $isbn;
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
    public function handle()
    {
        $volumeInfo = $this->volumeInfo;
        $isbn = $this->isbn;
        $authors = $volumeInfo['authors'] ?? [];

        $book = Book::updateOrCreate(
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
