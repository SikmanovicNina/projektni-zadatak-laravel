<?php

namespace App\Services;

use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetService
{
    /**
     * Send the password reset link to the user.
     *
     * @param string $email
     * @throws ValidationException
     */
    public function sendResetLink(string $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We can\'t find a user with that email address.'],
            ]);
        }

        event(new PasswordResetRequested($user));
    }

    /**
     * Reset the user's password using a valid token.
     *
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data)
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status;
    }
}
