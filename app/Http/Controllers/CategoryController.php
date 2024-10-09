<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
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

        $categories = Category::filter($request->only(['search']))->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('icon')) {
            $validatedData['icon'] = $this->setPicturePath($request);
        }

        $category = Category::create($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');

            if ($category->icon) {
                $this->deletePicture($category);
            }

            $validatedData['icon'] = $path;
        }

        $category->update($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        if ($category->icon) {
            $this->deletePicture($category);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 200);
    }

    /**
     * Handle the uploading and storage of the category's picture.
     *
     * @param Request $request
     * @return string The path where the picture is stored.
     */
    private function setPicturePath($request)
    {
        return $request->file('icon')->store('icons', 'public');
    }

    /**
     * Delete the category's picture from storage.
     *
     * @param Category $category
     * @return void
     */
    private function deletePicture(Category $category)
    {
        Storage::disk('public')->delete($category->icon);
    }
}
