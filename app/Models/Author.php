<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    public const PER_PAGE_OPTIONS = [20, 50, 100];
    protected $fillable = [
        'first_name',
        'last_name',
        'biography',
        'picture'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn ($query, $search) =>
        $query->where(fn ($query) =>
        $query->where('first_name', 'like', '%' . $search . '%')
              ->orWhere('last_name', 'like', '%' . $search . '%')));

        return $query;
    }
}
