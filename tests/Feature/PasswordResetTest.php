<?php

use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});

it('can send reset password email with valid email', function () {
    Event::fake();

    User::factory()->create([
        'role_id' => User::ROLE_LIBRARIAN,
        'email' => 'user@email.com',
    ]);

    $response = $this->postJson(route('password.reset-request'), ['email' => 'user@email.com']);

    $response->assertStatus(200);

    Event::assertDispatched(PasswordResetRequested::class);
});

it('can reset password with valid data', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $token = Password::broker()->createToken($user);

    $response = $this->postJson(route('password.reset'), [
        'email' => 'user@example.com',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
        'token' => $token,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Password has been reset successfully.']);

    expect(Hash::check('newpassword', $user->fresh()->password))->toBeTrue();
});

it('cannot reset password without token', function () {
    User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->postJson(route('password.reset'), [
        'email' => 'user@example.com',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['token']);
});
