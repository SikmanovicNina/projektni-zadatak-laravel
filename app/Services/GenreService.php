<?php

namespace App\Services;

use App\Models\Genre;

class GenreService
{
    /**
     * Get all genres with filtering and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAllGenres(array $filters, int $perPage)
    {
        return Genre::filter($filters)->paginate($perPage);
    }

    /**
     * Create a new genre.
     *
     * @param array $data
     * @return Genre
     */
    public function createGenre(array $data)
    {
        return Genre::create($data);
    }

    /**
     * Update an existing genre.
     *
     * @param Genre $genre
     * @param array $data
     * @return Genre
     */
    public function updateGenre(Genre $genre, array $data)
    {
        $genre->update($data);
        return $genre;
    }

    /**
     * Delete a genre.
     *
     * @param Genre $genre
     * @return void
     */
    public function deleteGenre(Genre $genre)
    {
        $genre->delete();
    }
}
