<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Http\Resources\GenreResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Genre;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __construct(protected GenreService $genreService)
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

        $filters = $request->only(['search']);
        $genres = $this->genreService->getAllGenres($filters, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($genres, GenreResource::class)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GenreRequest $request
     * @return JsonResponse
     */
    public function store(GenreRequest $request)
    {
        $validatedData = $request->validated();

        $genre = $this->genreService->createGenre($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new GenreResource($genre)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Genre $genre
     * @return JsonResponse
     */
    public function show(Genre $genre)
    {
        return response()->json([
            'status' => 'success',
            'data' => new GenreResource($genre)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GenreRequest $request
     * @param Genre $genre
     * @return JsonResponse
     */
    public function update(GenreRequest $request, Genre $genre)
    {
        $validatedData = $request->validated();

        $genre = $this->genreService->updateGenre($genre, $validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new GenreResource($genre)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Genre $genre
     * @return JsonResponse
     */
    public function destroy(Genre $genre)
    {
        $this->genreService->deleteGenre($genre);

        return response()->json(['message' => 'Genre deleted successfully.']);
    }
}
