<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Filterable;

    public const ROLE_LIBRARIAN = 2;

    public const PER_PAGE_OPTIONS = [20, 50, 100];

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'jmbg',
        'role_id',
        'email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $this->applyFilters($query, $filters, ['first_name', 'last_name', 'username']);
    }
}
