<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Image;

class ImageService
{
    /**
     * Store a new image for a given book.
     *
     * @param Book $book
     * @param $imageFile
     * @return string
     */
    public function storeImage(Book $book, $imageFile)
    {
        $path = $imageFile->store('book-images', 'public');

        $book->images()->create([
            'image' => $path,
            'cover_image' => false,
        ]);

        return $path;
    }

    /**
     * Update the cover image for a book by image ID.
     *
     * @param Book $book
     * @param int $imageId
     * @return Image
     */
    public function updateCoverImage(Book $book, int $imageId)
    {
        $book->images()->update(['cover_image' => false]);

        $image = Image::findOrFail($imageId);
        $image->cover_image = true;
        $image->save();

        return $image;
    }
}
