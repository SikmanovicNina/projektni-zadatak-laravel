<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        if (!in_array($perPage, Author::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $categories = Author::filter($request->only(['search']))->paginate($perPage);

        return AuthorResource::collection($categories);
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

    public function show(Author $author)
    {
        return new AuthorResource($author);

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

    public function destroy(Author $author)
    {
        if ($author->picture) {
            Storage::disk('public')->delete($author->picture);
        }

        $author->delete();

        return response()->json(['message' => 'Author deleted successfully.'], 200);
    }
}
