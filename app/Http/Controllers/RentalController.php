<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Book;
use App\Models\Rental;
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
}
