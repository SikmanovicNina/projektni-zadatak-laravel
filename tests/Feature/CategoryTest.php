<?php

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    (new \Database\Seeders\RolesTableSeeder())->run();
});


it('can fetch categories', function () {
    authenticateLibrarian();

    $response = $this->getJson(route('categories.index'));
    $response->assertStatus(200);
});

it('cannot fetch categories if not authenticated', function () {

    $response = $this->getJson(route('categories.index'));
    $response->assertStatus(401);
});

it('can store a new category', function () {
    authenticateLibrarian();

    $file = UploadedFile::fake()->image('profile.jpg');

    $data = Category::factory()->raw(['icon' => $file]);

    $response = $this->postJson(route('categories.store'), $data);
    $response->assertStatus(200);
    $this->assertDatabaseHas('categories', [
        'name' => $data['name'],
        'description' => $data['description'],
    ]);

    Storage::disk('public')->assertExists('icons/' . $file->hashName());

});

it('cannot store a new category without a name', function () {
    authenticateLibrarian();

    $data = Category::factory()->raw();
    unset($data['name']);

    $response = $this->postJson(route('categories.store'), $data);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

it('can update category with a new picture', function () {
    authenticateLibrarian();

    $file = UploadedFile::fake()->image('profile.jpg');
    $category = Category::factory()->create(['icon' => $file]);

    Storage::disk('public')->put('icons/' . $file->hashName(), file_get_contents($file));

    $newFile = UploadedFile::fake()->image('new-profile.jpg');

    $updatedData = Category::factory()->raw(['icon' => $newFile]);

    $response = $this->putJson(route('categories.update', $category->id), $updatedData);
    $response->assertStatus(200);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => $updatedData['name'],
        'description' => $updatedData['description'],
    ]);

    Storage::disk('public')->assertMissing('icons/' . $category->icon);
    Storage::disk('public')->assertExists('icons/' . $newFile->hashName());
});

it('can retrieve a specific category', function () {
    authenticateLibrarian();

    $category = Category::factory()->create();

    $response = $this->getJson(route('categories.show', $category->id));
    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ]
    ]);
});

it('can delete a category', function () {
    authenticateLibrarian();

    $file = UploadedFile::fake()->image('profile.jpg');
    $category = Category::factory()->create(['icon' => $file]);

    Storage::disk('public')->put('icons/' . $file->hashName(), file_get_contents($file));

    $response = $this->deleteJson(route('categories.destroy', $category->id));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
        'name' => $category->name,
        'description' => $category->description,
    ]);

    Storage::disk('public')->assertMissing('icons/' . $category->icon);
});
