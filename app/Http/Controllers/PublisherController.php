<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Publisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    /**
     *  Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        if (!in_array($perPage, self::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $authors = Publisher::filter($request->only(['search']))->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($authors, PublisherResource::class)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PublisherRequest $request
     * @return JsonResponse
     */
    public function store(PublisherRequest $request)
    {
        $validatedData = $request->validated();

        $publisher = Publisher::create($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new PublisherResource($publisher)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Publisher $publisher
     * @return JsonResponse
     */
    public function show(Publisher $publisher)
    {
        return response()->json([
            'status' => 'success',
            'data' => new PublisherResource($publisher)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PublisherRequest $request
     * @param Publisher $publisher
     * @return JsonResponse
     */
    public function update(PublisherRequest $request, Publisher $publisher)
    {
        $validatedData = $request->validated();

        $publisher->update($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new PublisherResource($publisher)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Publisher $publisher
     * @return JsonResponse
     */
    public function destroy(Publisher $publisher)
    {
        $publisher->delete();

        return response()->json(['message' => 'Publisher deleted successfully.'], 200);
    }
}
