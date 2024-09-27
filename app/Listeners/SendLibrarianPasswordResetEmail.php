<?php

namespace App\Listeners;

use App\Events\LibrarianCreated;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class SendLibrarianPasswordResetEmail
{
    public function handle(LibrarianCreated $event)
    {
        $token = Password::createToken($event->librarian);

        Mail::to($event->librarian->email)->send(new ResetPasswordMail($token));
    }
}
