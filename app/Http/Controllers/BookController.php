<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Requests\DiscardRequest;
use App\Http\Resources\BookResource;
use App\Models\Discard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        if (!in_array($perPage, self::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $books = Book::filter($request->only(['search']))->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => BookResource::collection($books)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BookRequest $request
     * @return JsonResponse
     */
    public function store(BookRequest $request)
    {
        $validatedData = $request->validated();

        $book = Book::create($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new BookResource($book)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function show(Book $book)
    {
        return response()->json([
            'status' => 'success',
            'data' => new BookResource($book)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BookRequest $request
     * @param Book $book
     * @return JsonResponse
     */
    public function update(BookRequest $request, Book $book)
    {
        $validatedData = $request->validated();

        $book->update($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new BookResource($book)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully.'], 200);
    }

    /**
     * Discard a book from the library's inventory.
     *
     *  The function decreases the number of available copies for the book and, if no copies are left,
     *  the book is removed from the active inventory. A record of the discarded book is kept for audit purposes.
     *
     * @param DiscardRequest $request
     * @param Book $book
     * @return JsonResponse
     */
    public function discardBook(DiscardRequest $request, Book $book)
    {
        if ($book->number_of_copies <= 0) {
            return response()->json([
                'error' => 'This book cannot be discarded as it does not exist in the inventory.',
            ], 400);
        }

        $admin = auth()->user();

        $book->decrement('number_of_copies');

        if ($book->number_of_copies === 0) {
            $book->delete();
        }

        Discard::create([
            'book_id' => $book->id,
            'admin_id' => $admin->id,
        ]);

        return response()->json([
            'message' => 'Book discarded successfully.',
        ], 200);
    }

}
