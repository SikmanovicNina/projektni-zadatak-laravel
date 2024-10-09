<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Send a password reset email to the user.
     *
     *  This function validates the provided email address, checks if a user with that email exists,
     *  and if so, dispatches an event to send the password reset link. If the email address is not found,
     *  it throws a validation exception with an error message.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendResetPasswordEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We can\'t find a user with that email address.'],
            ]);
        }

        event(new PasswordResetRequested($user));

        return response()->json(['message' => 'Password reset link sent to your email.'], 200);
    }

    /**
     * Reset the user's password using a valid token.
     *
     * This function validates the password reset token, email, and password input. If the token is valid
     * and the password reset is successful, it updates the user's password and returns a success message.
     * Otherwise, it returns an error message indicating why the reset failed.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully.'], 200);
        } else {
            return response()->json(['message' => __($status)], 400);
        }
    }
}
