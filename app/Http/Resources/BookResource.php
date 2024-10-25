<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'number_of_pages' => $this->number_of_pages,
            'number_of_copies' => $this->number_of_copies,
            'isbn' => $this->isbn,
            'language' => $this->language,
            'script' => $this->script,
            'binding' => $this->binding,
            'dimensions' => $this->dimensions,
            'images' => $this->images,
            'categories' => $this->categories,
            'publishers' => $this->publishers,
            'authors' => $this->authors,
            'genres' => $this->genres
        ];
    }
}
