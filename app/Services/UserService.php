<?php

namespace App\Services;

use App\Events\PasswordResetRequested;
use App\Models\User;

class UserService
{
    /**
     * Get all users with pagination and filtering.
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAllUsers(array $filters, int $perPage)
    {
        return User::latest()->filter($filters)->paginate($perPage);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data)
    {
        $user = User::create($data);

        if ($data['role_id'] === User::ROLE_LIBRARIAN) {
            event(new PasswordResetRequested($user));
        }

        return $user;
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return void
     */
    public function deleteUser(User $user)
    {
        $user->delete();
    }

    /**
     * Upload the user's profile picture.
     *
     * @param User $user
     * @param $picture
     * @return string
     */
    public function uploadProfilePicture(User $user, $picture)
    {
        $path = $picture->store('user-images', 'public');
        $user->profile_picture = $path;
        $user->save();

        return $path;
    }
}
