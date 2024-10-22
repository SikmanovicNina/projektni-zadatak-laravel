<?php

use App\Models\Publisher;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});


it('can fetch publishers', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('publishers.index'));
    $response->assertStatus(200);
});

it('cannot fetch publishers if not authenticated', function () {

    $response = $this->getJson(route('publishers.index'));
    $response->assertStatus(401);
});

it('can store a new publisher', function () {
    authenticateLibrarian();

    $data = Publisher::factory()->raw();

    $response = $this->postJson(route('publishers.store'), $data);
    $response->assertStatus(200);
    $this->assertDatabaseHas('publishers', $data);
});

it('cannot store a new publisher without a name', function () {
    authenticateLibrarian();

    $data = Publisher::factory()->raw();
    unset($data['name']);

    $response = $this->postJson(route('publishers.store'), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

it('can update publisher', function () {
    authenticateLibrarian();

    $publisher = Publisher::factory()->create();

    $updatedData = Publisher::factory()->raw();

    $response = $this->putJson(route('publishers.update', $publisher->id), $updatedData);
    $response->assertStatus(200);
    $this->assertDatabaseHas('publishers', $updatedData);
});

it('can retrieve a specific publisher', function () {
    authenticateLibrarian();

    $publisher = Publisher::factory()->create();

    $response = $this->getJson(route('publishers.show', $publisher->id));
    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $publisher->id,
            'name' => $publisher->name,
            'address' => $publisher->address,
            'website' => $publisher->website,
            'email' => $publisher->email,
            'phone_number' => $publisher->phone_number,
            'established_year' => (string) $publisher->established_year,
        ]
    ]);
});

it('can delete a publisher', function () {
    authenticateLibrarian();

    $publisher = Publisher::factory()->create();

    $response = $this->deleteJson(route('publishers.destroy', $publisher->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('publishers', ['id' => $publisher->id]);
});
