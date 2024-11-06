<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AuthorService
{
    /**
     * Get all authors with pagination and filtering.
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAllAuthors(array $filters, int $perPage = 20)
    {
        return Author::filter($filters)->paginate($perPage);
    }

    /**
     * Create a new author with optional picture upload.
     *
     * @param array $data
     * @return Author
     */
    public function createAuthor(array $data)
    {
        if (!empty($data['picture'])) {
            $data['picture'] = $this->storePicture($data['picture']);
        }

        return Author::create($data);
    }

    /**
     * Update an existing author with optional picture replacement.
     *
     * @param Author $author
     * @param array $data
     * @return Author
     */
    public function updateAuthor(Author $author, array $data)
    {
        if (!empty($data['picture'])) {
            $this->handleAuthorPicture($author, $data['picture']);
        }

        $author->update($data);
        return $author;
    }

    /**
     * Delete an author and their associated picture.
     *
     * @param Author $author
     * @return void
     */
    public function deleteAuthor(Author $author)
    {
        if ($author->picture) {
            $this->deletePicture($author->picture);
        }
        $author->delete();
    }

    /**
     * Store the picture file and return its path.
     *
     * @param UploadedFile $picture
     * @return string
     */
    private function storePicture(UploadedFile $picture)
    {
        return $picture->store('author-images', 'public');
    }

    /**
     * Delete the picture file from storage.
     *
     * @param string $picturePath
     * @return void
     */
    private function deletePicture(string $picturePath)
    {
        Storage::disk('public')->delete($picturePath);
    }

    /**
     * Handles updating the author's profile picture.
     * Deletes the existing picture if it exists, then stores the new picture.
     *
     * @param Author $author The author object that contains the current picture data.
     * @param mixed $newPicture The new picture data to be stored for the author.
     */
    private function handleAuthorPicture(Author $author, UploadedFile $newPicture)
    {
        if (!empty($author->picture)) {
            $this->deletePicture($author->picture);
        }

        $author->picture = $this->storePicture($newPicture);
    }
}
