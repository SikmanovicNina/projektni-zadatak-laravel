<?php

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});


it('can fetch rented books', function () {
    authenticateLibrarian();

    $librarian = User::factory()->create(['role_id' => User::ROLE_LIBRARIAN]);
    $student = User::factory()->create(['role_id' => User::ROLE_STUDENT]);
    $book = Book::factory()->create(['number_of_copies' => 5]);

    Rental::factory()->create([
        'book_id' => $book->id,
        'student_id' => $student->id,
        'librarian_id' => $librarian->id,
        'rented_at' => now(),
        'returned_at' => null,
    ]);

    $response = $this->getJson(route('rentals.status', ['status' => 'rented']));

    $response->assertStatus(200)->assertJson(['status' => 'success']);
});

it('can fetch returned books', function () {
    authenticateLibrarian();

    $librarian = User::factory()->create(['role_id' => User::ROLE_LIBRARIAN]);
    $student = User::factory()->create(['role_id' => User::ROLE_STUDENT]);
    $book = Book::factory()->create(['number_of_copies' => 5]);

    Rental::factory()->create([
        'book_id' => $book->id,
        'student_id' => $student->id,
        'librarian_id' => $librarian->id,
        'rented_at' => now()->subDays(5),
        'returned_at' => now()->subDays(1),
    ]);

    $response = $this->getJson(route('rentals.status', ['status' => 'returned']));

    $response->assertStatus(200)->assertJson(['status' => 'success']);
});

it('can fetch overdue books', function () {
    authenticateLibrarian();

    $librarian = User::factory()->create(['role_id' => User::ROLE_LIBRARIAN]);
    $student = User::factory()->create(['role_id' => User::ROLE_STUDENT]);
    $book = Book::factory()->create(['number_of_copies' => 5]);

    Rental::factory()->create([
        'book_id' => $book->id,
        'student_id' => $student->id,
        'librarian_id' => $librarian->id,
        'rented_at' => now()->subDays(40),
    ]);

    $response = $this->getJson(route('rentals.status', ['status' => 'overdue']));

    $response->assertStatus(200)->assertJson(['status' => 'success']);
});

it('returns an error for invalid status', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('rentals.status', ['status' => 'invalid']));

    $response->assertStatus(404);
});

it('can rent a book', function () {
    authenticateLibrarian();

    $librarian = User::factory()->create(['role_id' => User::ROLE_LIBRARIAN]);
    $student = User::factory()->create(['role_id' => User::ROLE_STUDENT]);
    $book = Book::factory()->create(['number_of_copies' => 1]);

    $data = [
        'book_id' => $book->id,
        'student_id' => $student->id,
        'librarian_id' => $librarian->id,
    ];

    $response = $this->postJson(route('rentals.rent'), $data);

    $response->assertStatus(200)->assertJson(['status' => 'success']);
});

it('returns an error when renting an unavailable book', function () {
    authenticateLibrarian();

    $librarian = User::factory()->create(['role_id' => User::ROLE_LIBRARIAN]);
    $student = User::factory()->create(['role_id' => User::ROLE_STUDENT]);
    $book = Book::factory()->create(['number_of_copies' => 0]);

    $data = [
        'book_id' => $book->id,
        'student_id' => $student->id,
        'librarian_id' => $librarian->id,
    ];

    $response = $this->postJson(route('rentals.rent'), $data);

    $response->assertStatus(422)
        ->assertJson(['message' => "You can't rent out books if there are none in the library as they were all rented out."]);
});

it('can return a rented book', function () {
    authenticateLibrarian();

    $librarian = User::factory()->create(['role_id' => User::ROLE_LIBRARIAN]);
    $student = User::factory()->create(['role_id' => User::ROLE_STUDENT]);
    $book = Book::factory()->create(['number_of_copies' => 5]);

    $rental = Rental::factory()->create([
        'book_id' => $book->id,
        'student_id' => $student->id,
        'librarian_id' => $librarian->id,
        'rented_at' => now()->subDays(5),
        'returned_at' => null,
    ]);

    $response = $this->postJson(route('rentals.return', $rental->id), [
        'book_id' => $book->id,
        'student_id' => $student->id,
        'librarian_id' => $librarian->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Book returned successfully.',
            'overdue_days' => 0,
        ]);

});
