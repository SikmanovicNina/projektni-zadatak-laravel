<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Author;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct(protected AuthorService $authorService)
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

        $authors = $this->authorService->getAllAuthors($filters, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($authors, AuthorResource::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AuthorRequest $request
     * @return JsonResponse
     */
    public function store(AuthorRequest $request)
    {
        $validatedData = $request->validated();
        $picture = $request->file('picture');

        $author = $this->authorService->createAuthor($validatedData, $picture);

        return response()->json([
            'status' => 'success',
            'data' => new AuthorResource($author)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Author $author
     * @return JsonResponse
     */
    public function show(Author $author)
    {
        return response()->json([
            'status' => 'success',
            'data' => new AuthorResource($author)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AuthorRequest $request
     * @param Author $author
     * @return JsonResponse
     */
    public function update(AuthorRequest $request, Author $author)
    {
        $validatedData = $request->validated();
        $image = $request->file('image');

        $author = $this->authorService->updateAuthor($author, $validatedData, $image);

        return response()->json([
            'status' => 'success',
            'data' => new AuthorResource($author)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Author $author
     * @return JsonResponse
     */
    public function destroy(Author $author)
    {
        $this->authorService->deleteAuthor($author);

        return response()->json(['message' => 'Author deleted successfully.']);
    }
}
