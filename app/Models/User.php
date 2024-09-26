<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
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
        Log::info('filters', $filters);

        $query->when($filters['search'] ?? false, fn ($query, $search) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')
            ->orWhere('username', 'like', '%'.$search.'%')));

        $query->when($filters['role_id'] ?? false, fn ($query, $roleId) => $query->where('role_id', $roleId));

        return $query;
    }
}
