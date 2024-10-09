<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Store images for a specific book.
     *
     * @param Request $request
     * @param Book $book
     * @return JsonResponse
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePath = [];

        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('book-images', 'public');

            $book->images()->create([
                'image' => $path,
                'cover_image' => false,
            ]);

            $imagePath[] = $path;
        }


        return response()->json([
            'message' => 'Images uploaded successfully',
            'image' => $imagePath,
        ], 201);
    }

    /**
     * Update the cover image for the specified book by image ID.
     *
     * @param Request $request
     * @param Book $book
     * @return JsonResponse
     */
    public function updateCoverImage(Request $request, Book $book)
    {
        $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);

        $book->images()->update(['cover_image' => false]);

        $image = Image::findOrFail($request->image_id);

        $image->cover_image = true;
        $image->save();

        return response()->json([
            'message' => 'Cover image updated successfully',
            'cover_image_id' => $image->id,
        ], 200);
    }
}
