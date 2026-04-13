<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('reports')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        $category = Category::create($validated);
        logAdminAction('Create', 'Category', $category->id, "Created category: {$category->name}");

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'name_ar' => 'nullable|string|max:255',
        ]);

        $oldName = $category->name;
        $category->update($validated);
        logAdminAction('Update', 'Category', $category->id, "Updated category from '{$oldName}' to '{$category->name}'");

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category)
    {
        $categoryName = $category->name;
        $category->delete();
        logAdminAction('Delete', 'Category', $category->id, "Deleted category: {$categoryName}");

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée.');
    }
}