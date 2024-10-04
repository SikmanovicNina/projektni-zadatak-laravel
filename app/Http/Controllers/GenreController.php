<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Illuminate\Http\Request;

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

        return response()->json($genre, 201);
    }

    public function show(Genre $genre)
    {
        //
    }

    public function edit()
    {
        //
    }

    public function update(Request $request)
    {
        //
    }

    public function destroy(Genre $genre)
    {
        //
    }
}
