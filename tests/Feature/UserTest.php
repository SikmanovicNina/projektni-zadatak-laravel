<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder)->run();
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

it('can paginate users', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('users.index'));
    $response->assertStatus(200);

});

it('can store a new user', function () {
    authenticateLibrarian();

    $data = User::factory()->raw();

    $response = $this->postJson(route('users.index'), $data);

    $response->assertStatus(201);

    unset($data['password']);
    unset($data['email_verified_at']);
    unset($data['remember_token']);

    $this->assertDatabaseHas('users', $data);
});

it('can update user', function () {
    authenticateLibrarian();

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
    authenticateLibrarian();

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
    authenticateLibrarian();

    $user = User::factory()->create();
    $response = $this->deleteJson(route('users.destroy', $user->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
