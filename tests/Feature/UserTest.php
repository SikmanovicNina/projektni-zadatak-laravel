<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->librarian = User::factory()->create(['role_id' => User::ROLE_LIBRARIAN]);
    Sanctum::actingAs($this->librarian);
});

it('can paginate users', function () {

    $response = $this->get('/api/users');

    $response->assertStatus(200);

});

it('can store a new user', function () {

    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'e2@example.com',
        'username' => 'example123',
        'jmbg' => '1234557880122',
        'role_id' => 1,
        'password' => 'password123',
    ];

    $response = $this->postJson('/api/users', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('users', [
        'email' => 'e2@example.com',
        'username' => 'example123',
        'jmbg' => '1234557880122',
    ]);
});

it('can update user', function () {
    $user = User::factory()->create([
        'first_name' => 'OldName123',
        'last_name' => 'OldLastName123',
        'email' => 'old123@librarian.com',
        'username' => 'testOld123',
        'jmbg' => '1233566800033',
        'role_id' => 2,
    ]);

    $updatedData = [
        'first_name' => 'NewName123',
        'last_name' => 'NewLastName123',
        'email' => 'new123@librarian.com',
        'username' => 'testNew123',
        'jmbg' => '1233566800033',
        'role_id' => 2,
    ];

    $response = $this->putJson(route('users.update', $user->id), $updatedData);

    $response->assertStatus(200);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'first_name' => 'NewName123',
        'email' => 'new123@librarian.com',
    ]);
});

it('can retrieve a specific user', function () {

    $user = User::factory()->create([
        'first_name' => 'Name1234',
        'last_name' => 'LastName1234',
        'email' => '1234@librarian.com',
        'username' => 'testOld1234',
        'jmbg' => '1233566800030',
        'role_id' => 2,
    ]);

    $response = $this->getJson(route('users.show', $user->id));

    $response->assertStatus(200);

    $response->assertJson([
        'id' => $user->id,
        'first_name' => $user->first_name,
        'email' => $user->email,
        'role_id' => $user->role_id,
    ]);
});

it('can delete user', function () {
    $user = User::factory()->create();
    $response = $this->deleteJson(route('users.destroy', $user->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

