<?php

namespace App\Http\Controllers;

use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function __construct(protected PasswordResetService $passwordResetService)
    {
    }

    /**
     * Send a password reset email to the user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendResetPasswordEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $this->passwordResetService->sendResetLink($request->email);

        return response()->json(['message' => 'Password reset link sent to your email.']);
    }

    /**
     * Reset the user's password using a valid token.
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

        $data = $request->only('email', 'password', 'password_confirmation', 'token');

        $status = $this->passwordResetService->resetPassword($data);

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully.']);
        } else {
            return response()->json(['message' => __($status)], 400);
        }
    }
}
