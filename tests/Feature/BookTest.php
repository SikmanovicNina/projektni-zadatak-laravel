<?php

use App\Models\Book;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});


it('can fetch books', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('books.index'));
    $response->assertStatus(200);
});

it('cannot fetch books if not authenticated', function () {

    $response = $this->getJson(route('books.index'));
    $response->assertStatus(401);
});

it('can store a new book', function () {
    authenticateLibrarian();

    $data = Book::factory()->raw();

    $response = $this->postJson(route('books.store'), $data);
    $response->assertStatus(200);
    $this->assertDatabaseHas('books', $data);
});

it('cannot store a new book without a name', function () {
    authenticateLibrarian();

    $data = Book::factory()->raw();
    unset($data['name']);

    $response = $this->postJson(route('books.store'), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

it('can update book', function () {
    authenticateLibrarian();

    $book = Book::factory()->create();

    $updatedData = Book::factory()->raw();

    $response = $this->putJson(route('books.update', $book->id), $updatedData);
    $response->assertStatus(200);
    $this->assertDatabaseHas('books', $updatedData);
});

it('can retrieve a specific book', function () {
    authenticateLibrarian();

    $book = Book::factory()->create();

    $response = $this->getJson(route('books.show', $book->id));
    $response->assertStatus(200);
    unset($book['created_at'], $book['updated_at']);
    $response->assertJson([
        'status' => 'success',
        'data' => $book->toArray(),
    ]);
});

it('can delete a book', function () {
    authenticateLibrarian();

    $book = Book::factory()->create();

    $response = $this->deleteJson(route('books.destroy', $book->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});
