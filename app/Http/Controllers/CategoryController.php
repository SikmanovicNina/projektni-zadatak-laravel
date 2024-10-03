<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        //
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

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
