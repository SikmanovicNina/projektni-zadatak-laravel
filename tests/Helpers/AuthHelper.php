<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

function authenticateLibrarian()
{
    $librarian = User::factory()->create([
        'role_id' => User::ROLE_LIBRARIAN,
    ]);
    Sanctum::actingAs($librarian);

    return $librarian;
}
