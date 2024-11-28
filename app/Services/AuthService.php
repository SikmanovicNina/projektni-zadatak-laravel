<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Authenticate a user and return the access token.
     *
     * @param string $email
     * @param string $password
     * @return string|null
     */
    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user->createToken('api-token')->plainTextToken;
    }

    /**
     * Log out the authenticated user by revoking all API tokens.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user)
    {
        $user->tokens()->delete();
    }
}
