<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Policy;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class RentalService
{
    /**
     * Retrieves books based on their rental status.
     *
     * @param array $filters
     * @param string|null $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getBooksByStatus(array $filters, ?string $status, int $perPage): LengthAwarePaginator
    {
        $query = Rental::with(['book', 'student', 'librarian']);

        switch ($status) {
            case 'rented':
            case 'overdue':
                $query->whereNull('returned_at');
                break;
            case 'returned':
                $query->whereNotNull('returned_at');
                break;
        }

        if (!empty($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        $books = $query->paginate($perPage);

        if ($status === 'overdue') {
            $books->setCollection($books->getCollection()->filter(function ($rental) {
                $overdueDays = $this->calculateOverdueDays($rental);
                if ($overdueDays > 0) {
                    $rental->overdue_days = $overdueDays;
                    return true;
                }
                return false;
            }));
        }

        if (in_array($status, ['rented', 'overdue'])) {
            $books->each(function ($rental) {
                $rental->active_days_of_rental = $this->calculateDaysDifference($rental->rented_at);
            });
        }

        return $books;
    }

    /**
     * Processes renting a book.
     *
     * @param array $data
     * @return Rental
     * @throws ValidationException
     */
    public function rentBook(array $data): Rental
    {
        $book = Book::find($data['book_id']);

        if ($book->number_of_copies <= 0) {
            throw ValidationException::withMessages(['message' => "No copies available for rent."]);
        }

        $book->decrement('number_of_copies');

        $data['rented_at'] = now();
        return Rental::create($data);
    }

    /**
     * Processes returning a book.
     *
     * @param Rental $rental
     * @return array
     * @throws ValidationException
     */
    public function returnBook(Rental $rental): array
    {
        if ($rental->returned_at) {
            throw ValidationException::withMessages(['message' => 'This book has already been returned.']);
        }

        $rental->update(['returned_at' => now()]);

        $book = $rental->book;
        $book->increment('number_of_copies');

        return [
            'message' => 'Book returned successfully.',
            'overdue_days' => $this->calculateOverdueDays($rental),
        ];
    }

    /**
     * Provides a summary of the current rental status.
     *
     * @return array
     */
    public function getRentalSummary(): array
    {
        $rentals = Rental::whereNull('returned_at')->get();

        $rentedOverdue = $rentals->filter(fn ($rental) => $this->calculateOverdueDays($rental) > 0)->count();
        $rentedNotOverdue = $rentals->count() - $rentedOverdue;

        return [
            'status' => 'success',
            'rented_not_overdue' => $rentedNotOverdue,
            'rented_overdue' => $rentedOverdue,
        ];
    }

    /**
     * Calculates the number of overdue days for a specific rental.
     *
     * @param Rental $rental
     * @return int
     */
    private function calculateOverdueDays(Rental $rental): int
    {
        $rentalPolicy = Policy::where('name', Policy::RENTAL_PERIOD)->first();
        $dueDate = Carbon::parse($rental->rented_at)->addDays($rentalPolicy->period);
        return now()->greaterThan($dueDate) ? $this->calculateDaysDifference($dueDate) : 0;
    }

    /**
     * Calculates the day difference from the rental date to today.
     *
     * @param $startDate
     * @return int
     */
    private function calculateDaysDifference($startDate): int
    {
        return Carbon::parse($startDate)->diffInDays(now());
    }
}
