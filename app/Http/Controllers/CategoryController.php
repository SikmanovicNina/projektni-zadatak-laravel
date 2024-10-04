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

        if (!in_array($perPage, Category::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $categories = Category::filter($request->only(['search']))->paginate($perPage);

        return CategoryResource::collection($categories);
    }

    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');
            $validatedData['icon'] = $path;
        }

        $category = Category::create($validatedData);

        return new CategoryResource($category);

    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');

            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            $validatedData['icon'] = $path;
        }

        $category->update($validatedData);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 200);
    }
}
