<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LibrarianCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $librarian;

    public function __construct(User $librarian)
    {
        $this->librarian = $librarian;
    }
}
