<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->role_id === User::ROLE_LIBRARIAN) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return false;
    }

    public function view(User $user)
    {
        return false;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user)
    {
        return false;
    }

    public function delete(User $user)
    {
        return false;
    }

    public function uploadPicture(User $user)
    {
        return false;
    }
}
