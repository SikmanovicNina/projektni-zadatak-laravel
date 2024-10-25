<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;
    use Filterable;

    protected $hidden = ['pivot', 'created_at', 'updated_at'];

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

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

}
