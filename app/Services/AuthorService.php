<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorService
{
    public function getAllAuthors(Request $request, $perPage = 20)
    {
        return Author::filter($request->only(['search']))->paginate($perPage);
    }

    public function createAuthor(array $data, Request $request)
    {
        if ($request->hasFile('picture')) {
            $data['picture'] = $this->storePicture($request);
        }
        return Author::create($data);
    }

    public function updateAuthor(Author $author, array $data, Request $request)
    {
        if ($request->hasFile('picture')) {
            if ($author->picture) {
                $this->deletePicture($author->picture);
            }
            $data['picture'] = $this->storePicture($request);
        }
        $author->update($data);
        return $author;
    }

    public function deleteAuthor(Author $author)
    {
        if ($author->picture) {
            $this->deletePicture($author->picture);
        }
        $author->delete();
    }

    private function storePicture(Request $request)
    {
        return $request->file('picture')->store('author-images', 'public');
    }

    private function deletePicture($picturePath)
    {
        Storage::disk('public')->delete($picturePath);
    }
}
