<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
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

    public function show(Category $category)
    {
        return response()->json([
            'status' => 'success',
            'data' => new CategoryResource($category)
        ]);
    }

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

    public function destroy(Category $category)
    {
        if ($category->icon) {
            $this->deletePicture($category);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 200);
    }

    private function setPicturePath($request)
    {
        return $request->file('icon')->store('icons', 'public');
    }

    private function deletePicture(Category $category)
    {
        Storage::disk('public')->delete($category->icon);
    }
}
