<?php

namespace App\Services;

use App\Events\PasswordResetRequested;
use App\Models\User;

class UserService
{
    public function getAllUsers($filters, $perPage)
    {
        return User::latest()->filter($filters)->paginate($perPage);
    }

    public function createUser(array $data)
    {
        $user = User::create($data);

        if ($data['role_id'] === User::ROLE_LIBRARIAN) {
            event(new PasswordResetRequested($user));
        }

        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user)
    {
        return $user->delete();
    }

    public function uploadProfilePicture(User $user, $picture)
    {
        $path = $picture->store('user-images', 'public');
        $user->profile_picture = $path;
        $user->save();

        return $path;
    }
}
