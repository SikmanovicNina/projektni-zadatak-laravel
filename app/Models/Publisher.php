<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;
    use Filterable;

    public const PER_PAGE_OPTIONS = [20, 50, 100];
    protected $fillable = [
        'name',
        'address',
        'website',
        'email',
        'phone_number',
        'established_year',
    ];

    public function scopeFilter($query, array $filters)
    {
        $this->applyFilters($query, $filters, ['name', 'address', 'website', 'email', 'phone_number', 'established_year']);
    }
}
