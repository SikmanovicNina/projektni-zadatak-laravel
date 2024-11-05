<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\ResponseCollection;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function __construct(protected BookService $bookService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page', 20), self::PER_PAGE_OPTIONS)
            ? $request->input('per_page', 20)
            : 20;
        $filters = ['search' => $request->get('search')];

        $books = $this->bookService->getAllBooks($filters, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($books, BookResource::class)
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

        $book = $this->bookService->createBook($validatedData);

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

        $book = $this->bookService->updateBook($book, $validatedData);

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
        $this->bookService->deleteBook($book);

        return response()->json(['message' => 'Book deleted successfully.']);
    }

    /**
     * Discard a book from the library's inventory.
     *
     * @param Book $book
     * @return JsonResponse
     */
    public function discardBook(Book $book)
    {
        if ($book->number_of_copies <= 0) {
            return response()->json([
                'error' => 'This book cannot be discarded as it does not exist in the inventory.',
            ], 400);
        }

        $this->bookService->discardBook($book);

        return response()->json([
            'message' => 'Book discarded successfully.',
        ], 200);
    }
}
