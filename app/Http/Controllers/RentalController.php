<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;
use App\Http\Resources\RentalResource;
use App\Http\Resources\RentedBooksResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RentalController extends Controller
{
    public function __construct(protected RentalService $rentalService)
    {
    }

    /**
     * Retrieves books by rental status.
     *
     * @param Request $request
     * @param string|null $status
     * @return JsonResponse
     */
    public function getBooksByStatus(Request $request, $status = null)
    {
        $perPage = in_array($request->input('per_page', 20), self::PER_PAGE_OPTIONS)
            ? $request->input('per_page', 20)
            : 20;
        $filters = $request->only(['book_id', 'student_id']);

        $books = $this->rentalService->getBooksByStatus($filters, $status, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($books, RentedBooksResource::class)
        ]);
    }

    /**
     * Rents a book to a student upon librarian's request.
     *
     * @param RentalRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function rentBook(RentalRequest $request)
    {
        $validatedData = $request->validated();

        $rental = $this->rentalService->rentBook($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new RentalResource($rental)
        ]);
    }

    /**
     * Returns a book and updates rental information.
     *
     * @param RentalRequest $request
     * @param Rental $rental
     * @return JsonResponse
     * @throws ValidationException
     */
    public function returnBook(RentalRequest $request, Rental $rental): JsonResponse
    {
        $result = $this->rentalService->returnBook($rental);

        return response()->json($result);
    }

    /**
     * Displays a summary of the status of all rented books.
     *
     * @return JsonResponse
     */
    public function getRentalSummary()
    {
        $summary = $this->rentalService->getRentalSummary();

        return response()->json($summary);
    }
}
