<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index(Request $request)
    {

        $perPage = $request->input('per_page', 20);

        if (!in_array($perPage, self::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $authors = Publisher::filter($request->only(['search']))->paginate($perPage);

        return PublisherResource::collection($authors);
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
