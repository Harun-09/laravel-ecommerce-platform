<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Brand;
use App\Domains\ECommerce\Models\ProductImage;
use App\Domains\ECommerce\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view products')->only(['index', 'show']);
        $this->middleware('can:approve products')->only(['approve', 'reject', 'toggleFeatured']);
        $this->middleware('can:delete products')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Product::with(['vendor', 'category', 'brand', 'primaryImage']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('vendor')) {
            $query->where('vendor_id', $request->vendor);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::parents()->with('children')->ordered()->get();
        $vendors = Vendor::approved()->get();

        return view('admin.products.index', compact('products', 'categories', 'vendors'));
    }

    public function show(Request $request, Product $product)
    {
        $product->load([
            'vendor',
            'category',
            'brand',
            'variations.attributeValues.attribute',
            'reviews.user',
        ]);

        $showTrashedImages = $request->boolean('trashed_images');
        $imagesQuery = ProductImage::query()
            ->where('product_id', $product->id)
            ->orderBy('order')
            ->orderBy('id');

        if ($showTrashedImages) {
            $imagesQuery->onlyTrashed();
        }

        $images = $imagesQuery->paginate(12);
        $activeImagesCount = ProductImage::query()->where('product_id', $product->id)->count();
        $trashedImagesCount = ProductImage::onlyTrashed()->where('product_id', $product->id)->count();

        return view('admin.products.show', compact(
            'product',
            'images',
            'showTrashedImages',
            'activeImagesCount',
            'trashedImagesCount',
        ));
    }

    public function approve(Product $product)
    {
        $product->update([
            'status' => 'active',
            'rejection_reason' => null,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Product approved and published.');
    }

    public function reject(Request $request, Product $product)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $product->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'Product rejected.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['featured' => !$product->featured]);

        return back()->with('success', 'Product featured status updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
