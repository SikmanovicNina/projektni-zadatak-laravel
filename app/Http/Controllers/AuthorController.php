<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\CategoryResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function index()
    {
        //
    }

    public function store(AuthorRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('author-pictures', 'public');
            $validatedData['picture'] = $path;
        }

        $author = Author::create($validatedData);

        return new AuthorResource($author);
    }

    public function show(string $id)
    {
        //
    }

    public function update(AuthorRequest $request, Author $author)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('author-pictures', 'public');

            if ($author->picture) {
                Storage::disk('public')->delete($author->picture);
            }

            $validatedData['picture'] = $path;
        }

        $author->update($validatedData);

        return new AuthorResource($author);
    }

    public function destroy(string $id)
    {
        //
    }
}
