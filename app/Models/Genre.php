<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;
    use Filterable;

    protected $fillable = [
        'name',
        'description'
    ];

    public function scopeFilter($query, array $filters)
    {
        $this->applyFilters($query, $filters, ['name']);
    }

}
