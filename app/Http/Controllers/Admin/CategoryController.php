<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryTranslation;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('translations')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:categories,slug',
            'name_en' => 'required|string',
            'name_si' => 'nullable|string',
            'name_ta' => 'nullable|string',
        ]);

        $category = Category::create([
            'slug' => $request->slug,
            'status' => $request->has('status'),
        ]);

        $langs = ['en' => 'name_en', 'si' => 'name_si', 'ta' => 'name_ta'];
        foreach ($langs as $lang => $field) {
            if ($request->filled($field)) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'language_code' => $lang,
                    'name' => $request->$field,
                ]);
            }
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'slug' => 'required|unique:categories,slug,' . $category->id,
            'name_en' => 'required|string',
            'name_si' => 'nullable|string',
            'name_ta' => 'nullable|string',
        ]);

        $category->update([
            'slug' => $request->slug,
            'status' => $request->has('status'),
        ]);

        $langs = ['en' => 'name_en', 'si' => 'name_si', 'ta' => 'name_ta'];
        foreach ($langs as $lang => $field) {
            if ($request->filled($field)) {
                $category->translations()->updateOrCreate(
                    ['language_code' => $lang],
                    ['name' => $request->$field]
                );
            }
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Cannot delete this category because it contains products. Please delete or reassign its products first, or set its status to Inactive.');
            }
            return back()->with('error', 'An error occurred while deleting the category.');
        }
    }
}
