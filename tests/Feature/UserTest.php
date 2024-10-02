<?php

use App\Models\User;

it('can fetch a list of users', function () {
    User::factory()->count(30)->create();

    $response = $this->getJson('/api/users?per_page=10');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'role_id']
            ],
            'links',
            'meta'
        ])
        ->assertJsonCount(10, 'data');
});
