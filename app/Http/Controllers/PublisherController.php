<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Models\Publisher;

class PublisherController extends Controller
{
    public function index()
    {
        //
    }

    public function store(PublisherRequest $request)
    {
        $validatedData = $request->validated();

        $publisher = Publisher::create($validatedData);

        return new PublisherResource($publisher);
    }

    public function show(Publisher $publisher)
    {
        return new PublisherResource($publisher);
    }

    public function update(PublisherRequest $request, Publisher $publisher)
    {
        $validatedData = $request->validated();

        $publisher->update($validatedData);

        return new PublisherResource($publisher);
    }

    public function destroy(Publisher $publisher)
    {
        $publisher->delete();

        return response()->json(['message' => 'Publisher deleted successfully.'], 200);
    }
}
