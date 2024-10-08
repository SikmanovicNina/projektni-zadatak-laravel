<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'first_name',
        'last_name',
        'biography',
        'picture'
    ];

    public function publishers()
    {
        return $this->belongsToMany(Publisher::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $this->applyFilters($query, $filters, ['first_name', 'last_name']);
    }
}
