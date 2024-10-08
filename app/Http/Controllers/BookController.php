<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        //
    }

    public function store(BookRequest $request)
    {
        $validatedData = $request->validated();

        $book = Book::create($validatedData);

        return new BookResource($book);
    }

    public function show(string $id)
    {
        //
    }

    public function update(BookRequest $request, Book $book)
    {
        $validatedData = $request->validated();

        $book->update($validatedData);

        return new BookResource($book);
    }

    public function destroy(string $id)
    {
        //
    }
}
