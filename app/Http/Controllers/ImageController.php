<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoverImageRequest;
use App\Http\Requests\ImageRequest;
use App\Models\Book;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Store image for a specific book.
     *
     * @param Request $request
     * @param Book $book
     * @return JsonResponse
     */
    public function store(ImageRequest $request, Book $book)
    {
        $path = $request->file('image')->store('book-images', 'public');

        $book->images()->create([
            'image' => $path,
            'cover_image' => false,
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image_path' => $path,
        ], 201);
    }

    /**
     * Update the cover image for the specified book by image ID.
     *
     * @param Request $request
     * @param Book $book
     * @return JsonResponse
     */
    public function updateCoverImage(CoverImageRequest $request, Book $book)
    {
        $book->images()->update(['cover_image' => false]);

        $image = Image::findOrFail($request->image_id);
        $image->cover_image = true;
        $image->save();

        return response()->json([
            'message' => 'Cover image saved successfully',
            'cover_image_id' => $image->id,
        ], 200);
    }
}
