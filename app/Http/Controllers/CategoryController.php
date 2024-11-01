<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ResponseCollection;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search']);
        $perPage = in_array($request->input('per_page', 20), self::PER_PAGE_OPTIONS)
            ? $request->input('per_page', 20)
            : 20;

        $categories = $this->categoryService->getAllCategories($filters, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($categories, CategoryResource::class)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $icon = $request->file('icon');

        $category = $this->categoryService->storeCategory($validatedData, $icon);

        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $validatedData = $request->validated();
        $icon = $request->file('icon');

        $category = $this->categoryService->updateCategory($category, $validatedData, $icon);

        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);

        return response()->json(['message' => 'Category deleted successfully.'], 200);
    }
}
