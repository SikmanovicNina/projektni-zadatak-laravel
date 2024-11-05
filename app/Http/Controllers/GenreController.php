<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Http\Resources\GenreResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     *  Display a listing of the resource.
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

        $genres = Genre::filter($request->only(['search']))->paginate($perPage);

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

        $genre = Genre::create($validatedData);

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

        $genre->update($validatedData);

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
        $genre->delete();

        return response()->json(['message' => 'Genre deleted successfully.']);
    }
}
