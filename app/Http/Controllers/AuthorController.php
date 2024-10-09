<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
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

        $authors = Author::filter($request->only(['search']))->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => AuthorResource::collection($authors)
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

        if ($request->hasFile('picture')) {
            $validatedData['picture'] = $this->setPicturePath($request);
        }

        $author = Author::create($validatedData);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param Author $author
     * @return JsonResponse
     */
    public function destroy(Author $author)
    {
        if ($author->picture) {
            $this->deletePicture($author);
        }

        $author->delete();

        return response()->json(['message' => 'Author deleted successfully.'], 200);
    }

    /**
     * Handle the uploading and storage of the author's picture.
     *
     * @param Request $request
     * @return string The path where the picture is stored.
     */
    private function setPicturePath($request)
    {
        return $request->file('picture')->store('author-pictures', 'public');
    }

    /**
     * Delete the author's picture from storage.
     *
     * @param Author $author
     * @return void
     */
    private function deletePicture(Author $author)
    {
        Storage::disk('public')->delete($author->picture);
    }
}
