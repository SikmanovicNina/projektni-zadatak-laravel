<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Book;
use App\Models\Policy;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class RentalController extends Controller
{
    /**
     * Retrieve a list of books based on the rental status.
     *
     * @param $status
     * @return JsonResponse
     */
    public function getBooksByStatus($status = null)
    {
        $query = Rental::with(['book', 'student', 'librarian']);

        switch ($status) {
            case 'rented':
                $query
                    ->whereNull('returned_at');
                break;

            case 'returned':
                $query
                    ->whereNotNull('returned_at');
                break;

            case 'overdue':
                $query
                    ->whereNull('returned_at')
                    ->where('overdue_days', '>', 0);
                break;

            default:
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid status provided.',
                ], 400);
        }

        $books = $query->get();

        if (in_array($status, ['rented', 'overdue'])) {
            $books->each(function ($rental) {
                $rental->active_days_of_rental = $this->calculateDaysDifference($rental->rented_at);
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $books,
        ]);
    }

    /**
     *  Rent a book for a student by a librarian.
     *
     *  This method handles the process of renting a book by validating the
     *  data. It checks if the requested book is available for rent
     *  (i.e., if there are copies available).
     *  If available, it creates a new rental record, updates the number of
     *  available copies of the book, and returns a success response.
     *  If there are no available copies, it returns a validation error message.
     *
     * @param RentalRequest $request
     * @return JsonResponse
     */
    public function rentBook(RentalRequest $request)
    {
        $validatedData = $request->validated();

        $book = Book::find($validatedData['book_id']);

        if ($book->number_of_copies <= 0) {
            return response()->json([
                'message' => "You can't rent out books if there are none in the library as they were all rented out."
            ], 422);
        }

        $book->decrement('number_of_copies');

        $rental = Rental::create([
            'book_id' => $book->id,
            'student_id' => $validatedData['student_id'],
            'librarian_id' => $validatedData['librarian_id'],
            'rented_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => new RentalResource($rental)
        ]);
    }

    public function returnBook(RentalRequest $request, Rental $rental)
    {
        if (!$rental->returned_at) {
            $overdueDays = $this->calculateOverdueDays($rental);

            $rental->returned_at = now();
            $rental->overdue_days = $overdueDays;
            $rental->save();

            $book = $rental->book;
            $book->increment('number_of_copies');
            $book->save();

            return response()->json([
                'message' => 'Book returned successfully.',
                'overdue_days' => $overdueDays,
            ], 200);
        }

        return response()->json([
            'error' => 'This book was not rented out, hence cannot be returned.',
        ], 400);
    }

    private function calculateOverdueDays(Rental $rental)
    {
        $rentalPolicy = Policy::where('name', Policy::RENTAL_PERIOD)->first();
        $dueDate = Carbon::parse($rental->rented_at)->addDays($rentalPolicy->period);

        if (now()->greaterThan($dueDate)) {
            return $this->calculateDaysDifference($dueDate);
        }

        return 0;
    }

    private function calculateDaysDifference($startDate)
    {
        $from_date = Carbon::parse(date('Y-m-d', strtotime($startDate)));
        $through_date = Carbon::parse(date('Y-m-d', strtotime(now())));

        return $from_date->diffInDays($through_date);
    }
}
