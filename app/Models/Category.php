<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public const PER_PAGE_OPTIONS = [20, 50, 100];
    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn ($query, $search) =>
        $query->where('name', 'like', '%'.$search.'%'));

        return $query;
    }

}
