<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Publisher;
use App\Services\PublisherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function __construct(protected PublisherService $publisherService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page', 20), self::PER_PAGE_OPTIONS)
            ? $request->input('per_page', 20)
            : 20;

        $filters = $request->only(['search']);
        $publishers = $this->publisherService->getAllPublishers($filters, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($publishers, PublisherResource::class)
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

        $publisher = $this->publisherService->createPublisher($validatedData);

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

        $publisher = $this->publisherService->updatePublisher($publisher, $validatedData);

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
        $this->publisherService->deletePublisher($publisher);

        return response()->json(['message' => 'Publisher deleted successfully.']);
    }
}
