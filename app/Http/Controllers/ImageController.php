<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoverImageRequest;
use App\Http\Requests\ImageRequest;
use App\Models\Book;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    /**
     * Store image for a specific book.
     *
     * @param ImageRequest $request
     * @param Book $book
     * @return JsonResponse
     */
    public function store(ImageRequest $request, Book $book)
    {
        $image = $request->file('image');
        $path = $this->imageService->storeImage($book, $image);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image_path' => $path,
        ], 201);
    }

    /**
     * Update the cover image for the specified book by image ID.
     *
     * @param CoverImageRequest $request
     * @param Book $book
     * @return JsonResponse
     */
    public function updateCoverImage(CoverImageRequest $request, Book $book)
    {
        $imageId= $request->image_id;

        $image = $this->imageService->updateCoverImage($book, $imageId);

        return response()->json([
            'message' => 'Cover image saved successfully',
            'cover_image_id' => $image->id,
        ], 200);
    }
}
