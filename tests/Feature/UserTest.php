<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});

it('can login', function () {

    User::factory()->create([
        'email' => 'librarian@library.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson(route('login'), [
        'email' => 'librarian@library.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);

});

it('cannot login without email', function () {
    User::factory()->create([
        'email' => 'librarian@library.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson(route('login'), [
        'password' => 'password',
    ]);

    $response->assertStatus(422);

    $response->assertJsonValidationErrors('email');
});

it('can fetch users', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('users.index'));
    $response->assertStatus(200);
});

it('cannot fetch users if not authenticated', function () {

    $response = $this->getJson(route('users.index'));
    $response->assertStatus(401);
});

it('can store a new user', function () {
    authenticateLibrarian();

    $data = User::factory()->raw();

    $response = $this->postJson(route('users.store'), $data);

    $response->assertStatus(201);

    unset($data['password']);
    unset($data['email_verified_at']);
    unset($data['remember_token']);

    $this->assertDatabaseHas('users', $data);
});

it('cannot store a new user without a username', function () {
    authenticateLibrarian();

    $data = User::factory()->raw();

    unset($data['username']);
    $response = $this->postJson(route('users.store'), $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('username');
});

it('can update user', function () {
    authenticateLibrarian();

    $user = User::factory()->create();

    $updatedData = User::factory()->raw([
        'jmbg' => $user->jmbg,
        'role_id' => $user->role_id,
    ]);

    $response = $this->putJson(route('users.update', $user->id), $updatedData);

    $response->assertStatus(200);

    unset($updatedData['password']);
    unset($updatedData['email_verified_at']);
    unset($updatedData['remember_token']);

    $this->assertDatabaseHas('users', $updatedData);
});

it('can retrieve a specific user', function () {
    authenticateLibrarian();

    $user = User::factory()->create();

    $response = $this->getJson(route('users.show', $user->id));

    $response->assertStatus(200);

    $response->assertJson($user->toArray());
});

it('can delete user', function () {
    authenticateLibrarian();

    $user = User::factory()->create();
    $response = $this->deleteJson(route('users.destroy', $user->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
