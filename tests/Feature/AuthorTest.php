<?php

use App\Models\Author;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});

it('can fetch authors', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('authors.index'));
    $response->assertStatus(200);
});

it('cannot fetch authors if not authenticated', function () {

    $response = $this->getJson(route('authors.index'));
    $response->assertStatus(401);
});

it('can create an author', function () {
    authenticateLibrarian();

    $file = UploadedFile::fake()->image('profile.jpg');

    $data = Author::factory()->raw(['picture' => $file]);

    $response = $this->postJson(route('authors.store'), $data);
    $response->assertStatus(201);
    $this->assertDatabaseHas('authors', [
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'biography' => $data['biography'],
    ]);

    Storage::disk('public')->assertExists('author-pictures/' . $file->hashName());
});

it('can update an author with a new picture', function () {
    authenticateLibrarian();

    $file = UploadedFile::fake()->image('profile.jpg');
    $author = Author::factory()->create(['picture' => $file]);

    Storage::disk('public')->put('author-pictures/' . $file->hashName(), file_get_contents($file));

    $newFile = UploadedFile::fake()->image('new-profile.jpg');

    $updatedData = [
        'first_name' => 'Updated First Name',
        'last_name' => 'Updated Last Name',
        'biography' => 'Updated biography.',
        'picture' => $newFile,
    ];

    $response = $this->putJson(route('authors.update', $author->id), $updatedData);
    $response->assertStatus(200);
    $this->assertDatabaseHas('authors', [
        'id' => $author->id,
        'first_name' => $updatedData['first_name'],
        'last_name' => $updatedData['last_name'],
        'biography' => $updatedData['biography'],
    ]);

    Storage::disk('public')->assertMissing('author-pictures/' . $author->picture);
    Storage::disk('public')->assertExists('author-pictures/' . $newFile->hashName());
});

it('can delete an author', function () {
    authenticateLibrarian();

    $file = UploadedFile::fake()->image('profile.jpg');
    $author = Author::factory()->create(['picture' => $file]);

    Storage::disk('public')->put('author-pictures/' . $file->hashName(), file_get_contents($file));

    $response = $this->deleteJson(route('authors.destroy', $author->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('authors', [
        'id' => $author->id,
        'first_name' => $author->first_name,
        'last_name' => $author->last_name,
        'biography' => $author->biography,
    ]);

    Storage::disk('public')->assertMissing('author-pictures/' . $author->picture);
});
