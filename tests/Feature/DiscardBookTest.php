<?php

use App\Models\Book;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
    authenticateLibrarian();
});

it('can discard a book', function () {
    $book = Book::factory()->create(['number_of_copies' => 5]);

    $response = $this->postJson(route('books.discard', $book->id));

    $response->assertStatus(200);

    $this->assertEquals(4, $book->fresh()->number_of_copies);
});

it('removes book from inventory when no copies left', function () {
    $book = Book::factory()->create(['number_of_copies' => 1]);

    $response = $this->postJson(route('books.discard', $book->id));

    $response->assertStatus(200);

    $this->assertDatabaseMissing('books', [
        'id' => $book->id,
    ]);
});
