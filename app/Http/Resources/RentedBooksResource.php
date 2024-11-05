<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentedBooksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'book_id' => $this->book_id,
            'student_id' => $this->student_id,
            'librarian_id' => $this->librarian_id,
            'rented_at' => $this->rented_at,
            'returned_at' => $this->returned_at,
            'book' => $this->book,
            'student' => $this->book,
        ];

        if (isset($this->overdue_days)) {
            $data['overdue_days'] = $this->overdue_days;
        }

        if (isset($this->active_days_of_rental)) {
            $data['active_days_of_rental'] = $this->active_days_of_rental;
        }

        return $data;
    }
}
