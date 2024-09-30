<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
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
}
