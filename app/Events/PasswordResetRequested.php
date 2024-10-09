<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordResetRequested
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $librarian;

    public function __construct(User $librarian)
    {
        $this->librarian = $librarian;
    }
}
