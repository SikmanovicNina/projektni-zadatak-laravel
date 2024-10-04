<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $genres = Genre::filter($request->only(['search']))->paginate($perPage);

        return GenreResource::collection($genres);
    }

    public function store(GenreRequest $request)
    {
        $validatedData = $request->validated();

        $genre = Genre::create($validatedData);

        return new GenreResource($genre);
    }

    public function show(Genre $genre)
    {
        return new GenreResource($genre);
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $validatedData = $request->validated();

        $genre->update($validatedData);

        return new GenreResource($genre);

    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return response()->json(['message' => 'Genre deleted successfully.']);
    }
}
