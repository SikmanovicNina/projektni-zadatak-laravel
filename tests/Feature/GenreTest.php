<?php

use App\Models\Genre;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});


it('can fetch genres', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('genres.index'));
    $response->assertStatus(200);
});

it('cannot fetch genres if not authenticated', function () {

    $response = $this->getJson(route('genres.index'));
    $response->assertStatus(401);
});

it('can store a new genre', function () {
    authenticateLibrarian();

    $data = Genre::factory()->raw();

    $response = $this->postJson(route('genres.store'), $data);
    $response->assertStatus(200);
    $this->assertDatabaseHas('genres', $data);
});

it('cannot store a new genre without a name', function () {
    authenticateLibrarian();

    $data = genre::factory()->raw();
    unset($data['name']);

    $response = $this->postJson(route('genres.store'), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

it('can update genre', function () {
    authenticateLibrarian();

    $genre = Genre::factory()->create();

    $updatedData = Genre::factory()->raw();

    $response = $this->putJson(route('genres.update', $genre->id), $updatedData);
    $response->assertStatus(200);
    $this->assertDatabaseHas('genres', $updatedData);
});

it('can retrieve a specific genre', function () {
    authenticateLibrarian();

    $genre = Genre::factory()->create();

    $response = $this->getJson(route('genres.show', $genre->id));
    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $genre->id,
            'name' => $genre->name,
            'description' => $genre->description,
        ]
    ]);
});

it('can delete a genre', function () {
    authenticateLibrarian();

    $genre = Genre::factory()->create();

    $response = $this->deleteJson(route('genres.destroy', $genre->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
});
