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

        if (!in_array($perPage, self::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $authors = Author::filter($request->only(['search']))->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => AuthorResource::collection($authors)
        ]);
    }

    public function store(AuthorRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('picture')) {
            $validatedData['picture'] = $this->setPicturePath($request);
        }

        $author = Author::create($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new AuthorResource($author)
        ]);
    }

    public function show(Author $author)
    {
        return response()->json([
            'status' => 'success',
            'data' => new AuthorResource($author)
        ]);
    }

    public function update(AuthorRequest $request, Author $author)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('picture')) {
            $validatedData['picture'] = $this->setPicturePath($request);

            if ($author->picture) {
                $this->deletePicture($author);
            }
        }

        $author->update($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new AuthorResource($author)
        ]);
    }

    public function destroy(Author $author)
    {
        if ($author->picture) {
            $this->deletePicture($author);
        }

        $author->delete();

        return response()->json(['message' => 'Author deleted successfully.'], 200);
    }

    private function setPicturePath($request)
    {
        return $request->file('picture')->store('author-pictures', 'public');
    }

    private function deletePicture(Author $author)
    {
        Storage::disk('public')->delete($author->picture);
    }
}
