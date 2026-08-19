<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view categories')->only(['index']);
        $this->middleware('can:create categories')->only(['create', 'store']);
        $this->middleware('can:edit categories')->only(['edit', 'update']);
        $this->middleware('can:delete categories')->only(['destroy', 'restore', 'forceDestroy']);
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed');
        $query = Category::withCount('products', 'children');

        if ($showTrashed) {
            $query->onlyTrashed();
        }

        if ($request->filled('parent')) {
            $query->where('parent_id', $request->parent);
        } else {
            $query->whereNull('parent_id');
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->ordered()->paginate(20);
        $parentCategories = Category::withTrashed()->parents()->ordered()->get();
        $activeCount = Category::count();
        $trashedCount = Category::onlyTrashed()->count();

        return view('admin.categories.index', compact(
            'categories',
            'parentCategories',
            'activeCount',
            'trashedCount',
            'showTrashed',
        ));
    }

    public function create()
    {
        $parentCategories = Category::parents()->ordered()->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['image', 'remove_image']);
        $data['slug'] = Str::slug($request->name);
        $data['parent_id'] = $request->filled('parent_id') ? (int) $request->parent_id : null;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['featured'] = $request->boolean('featured', false);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::parents()
            ->where('id', '!=', $category->id)
            ->ordered()
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['image', 'remove_image']);
        $data['parent_id'] = $request->filled('parent_id') ? (int) $request->parent_id : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['featured'] = $request->boolean('featured', false);

        if ($request->boolean('remove_image') && $category->image) {
            Storage::disk('public')->delete($category->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with products.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category with subcategories.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category moved to trash successfully.');
    }

    public function restore(int $categoryId)
    {
        $category = Category::onlyTrashed()->findOrFail($categoryId);

        if ($category->parent_id) {
            $parent = Category::withTrashed()->find($category->parent_id);
            if ($parent && $parent->trashed()) {
                return back()->with('error', 'Restore parent category first.');
            }
        }

        $category->restore();

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', 'Category restored successfully.');
    }

    public function forceDestroy(int $categoryId)
    {
        $category = Category::onlyTrashed()->findOrFail($categoryId);

        if ($category->products()->withTrashed()->exists()) {
            return back()->with('error', 'Cannot permanently delete category with products.');
        }

        if ($category->children()->withTrashed()->exists()) {
            return back()->with('error', 'Cannot permanently delete category with subcategories.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->forceDelete();

        return redirect()->route('admin.categories.index', ['trashed' => 1])
            ->with('success', 'Category permanently deleted.');
    }
}
