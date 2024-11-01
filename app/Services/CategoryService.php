<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    /**
     * Get all categories with pagination and filtering.
     */
    public function getAllCategories(array $filters, int $perPage)
    {
        return Category::filter($filters)->paginate($perPage);
    }

    /**
     * Store a new category with an optional icon.
     */
    public function storeCategory(array $data, ?UploadedFile $icon = null): Category
    {
        if ($icon) {
            $data['icon'] = $this->setPicturePath($icon);
        }

        return Category::create($data);
    }

    /**
     * Update an existing category with optional new icon.
     */
    public function updateCategory(Category $category, array $data, ?UploadedFile $icon = null): Category
    {
        if ($icon) {
            if ($category->icon) {
                $this->deletePicture($category);
            }
            $data['icon'] = $this->setPicturePath($icon);
        }

        $category->update($data);
        return $category;
    }

    /**
     * Delete a category along with its associated icon.
     */
    public function deleteCategory(Category $category): void
    {
        if ($category->icon) {
            $this->deletePicture($category);
        }

        $category->delete();
    }

    /**
     * Store the icon file and return its path.
     */
    public function setPicturePath(UploadedFile $file): string
    {
        return $file->store('icons', 'public');
    }

    /**
     * Delete the icon file from storage.
     */
    public function deletePicture(Category $category): void
    {
        Storage::disk('public')->delete($category->icon);
    }
}
