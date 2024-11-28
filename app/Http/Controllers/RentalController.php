<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;
use App\Http\Resources\RentalResource;
use App\Http\Resources\RentedBooksResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Book;
use App\Models\Policy;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    /**
     * Retrieve a list of books based on the rental status.
     *
     * @param Request $request
     * @param null $status
     * @return JsonResponse
     */
    public function getBooksByStatus(Request $request, $status = null)
    {
        $perPage = $request->input('per_page', 20);

        if (!in_array($perPage, self::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $query = Rental::with(['book', 'student', 'librarian']);

        switch ($status) {
            case 'rented':
            case 'overdue':
                $query->whereNull('returned_at');
                break;

            case 'returned':
                $query->whereNotNull('returned_at');
                break;

            default:
                break;
        }

        if ($request->has('book_id')) {
            $query->where('book_id', $request->get('book_id'));
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->get('student_id'));
        }

        $books = $query->paginate($perPage);

        if ($status === 'overdue') {
            $filteredCollection = $books->getCollection()->filter(function ($rental) {
                $overdueDays = $this->calculateOverdueDays($rental);

                if ($overdueDays > 0) {
                    $rental->overdue_days = $overdueDays;
                    return true;
                }

                return false;
            });

            $books->setCollection($filteredCollection);
        }

        if (in_array($status, ['rented', 'overdue'])) {
            $books->each(function ($rental) {
                $rental->active_days_of_rental = Carbon::parse($rental->rented_at)->calculateDaysDifference();
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($books, RentedBooksResource::class)
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

        $validatedData['rented_at'] = now();
        $rental = Rental::create($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new RentalResource($rental)
        ]);
    }

    /**
     * Handles the process of returning a rented book.
     *
     *  This method checks if the book has already been returned. If not, it marks
     *  the book as returned by updating the `returned_at` timestamp, increments
     *  the number of available copies of the book in the database, calculates
     *  any overdue days, and returns a success response with the number of
     *  overdue days. If the book has already been returned, it returns an error response.
     *
     * @param RentalRequest $request
     * @param Rental $rental
     * @return JsonResponse
     */
    public function returnBook(RentalRequest $request, Rental $rental)
    {
        if (!$rental->returned_at) {
            $rental->returned_at = now();
            $rental->save();

            $book = $rental->book;
            $book->increment('number_of_copies');
            $book->save();

            $overdueDays = $this->calculateOverdueDays($rental);

            return response()->json([
                'message' => 'Book returned successfully.',
                'overdue_days' => $overdueDays,
            ], 200);
        }

        return response()->json([
            'error' => 'This book was not rented out, hence cannot be returned.',
        ], 400);
    }

    /**
     * Calculates the number of overdue days for a given rental.
     *
     * @param Rental $rental
     * @return float|int
     */
    private function calculateOverdueDays(Rental $rental)
    {
        $rentalPolicy = Policy::where('name', Policy::RENTAL_PERIOD)->first();
        $dueDate = Carbon::parse($rental->rented_at)->addDays($rentalPolicy->period);

        if (now()->greaterThan($dueDate)) {
            return $dueDate->calculateDaysDifference();
        }

        return 0;
    }

    /**
     *  Get a summary of the current rental status in the library.
     *
     *  It returns a summary of:
     *  - The number of books that are currently rented but not overdue.
     *  - The number of books that are overdue based on the rental policy.
     *
     * @return JsonResponse
     */
    public function getRentalSummary()
    {
        $rentals = Rental::whereNull('returned_at')->get();

        $rentedOverdue = 0;
        $rentedNotOverdue = 0;

        foreach ($rentals as $rental) {
            $overdueDays = $this->calculateOverdueDays($rental);

            if ($overdueDays === 0) {
                $rentedNotOverdue++;
            } else {
                $rentedOverdue++;
            }
        }

        return response()->json([
            'status' => 'success',
            'rented_not_overdue' => $rentedNotOverdue,
            'rented_overdue' => $rentedOverdue,
        ]);
    }

}
