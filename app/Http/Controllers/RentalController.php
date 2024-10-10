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
            $rentalPolicy = Policy::where('name', Policy::RENTAL_PERIOD)->first();
            $dueDate = Carbon::parse($rental->rented_at)->addDays($rentalPolicy->period);

            $isOverdue = now()->greaterThan($dueDate);
            $overdueDays = $isOverdue ? now()->diffInDays($dueDate) : 0;

            $rental->due_date = $dueDate;
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

}
