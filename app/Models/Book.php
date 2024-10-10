<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    use Filterable;

    public const SCRIPTS = ['Cyrillic', 'Latin', 'Arabic'];
    public const BINDINGS = ['Hardcover', 'Paperback', 'Spiral-bound'];
    public const DIMENSIONS = ['A1', 'A2', 'A3', '21cm x 29.7cm', '15cm x 21cm'];

    protected $fillable = [
        'name',
        'description',
        'number_of_pages',
        'number_of_copies',
        'isbn',
        'language',
        'script',
        'binding',
        'dimensions',
    ];

    public function scopeFilter($query, array $filters)
    {
        $this->applyFilters($query, $filters, ['name', 'description', 'language', 'isbn']);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function publishers()
    {
        return $this->belongsToMany(Publisher::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function rentals(){
        return $this->hasMany(Rental::class);
    }
}
