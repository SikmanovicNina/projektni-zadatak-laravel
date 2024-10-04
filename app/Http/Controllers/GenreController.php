<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Http\Resources\GenreResource;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        //
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
        //
    }
}
