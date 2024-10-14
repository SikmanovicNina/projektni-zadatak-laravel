<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discard extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'admin_id',
    ];

    /**
     * Get the book that was discarded.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the admin who discarded the book.
     */
    public function admin()
    {
        return $this->belongsTo(User::class);
    }
}
