<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    use Filterable;

    public function publishers()
    {
        return $this->belongsToMany(Publisher::class);
    }
    public const PER_PAGE_OPTIONS = [20, 50, 100];
    protected $fillable = [
        'first_name',
        'last_name',
        'biography',
        'picture'
    ];

    public function scopeFilter($query, array $filters)
    {
        $this->applyFilters($query, $filters, ['first_name', 'last_name']);
    }
}
