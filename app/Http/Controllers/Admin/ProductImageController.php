<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:edit products')->only(['store', 'update', 'restore']);
        $this->middleware('can:delete products')->only(['destroy', 'forceDestroy']);
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $shouldBePrimary = $request->boolean('is_primary')
            || !ProductImage::query()->where('product_id', $product->id)->where('is_primary', true)->exists();

        if ($shouldBePrimary) {
            $this->clearPrimaryFlags($product);
        }

        ProductImage::create([
            'product_id' => $product->id,
            'image' => $request->file('image')->store('products', 'public'),
            'alt_text' => $validated['alt_text'] ?? null,
            'order' => (int) ($validated['order']
                ?? ((ProductImage::query()->where('product_id', $product->id)->max('order') ?? -1) + 1)),
            'is_primary' => $shouldBePrimary,
        ]);

        return back()->with('success', 'Product image added successfully.');
    }

    public function update(Request $request, Product $product, int $imageId)
    {
        $image = ProductImage::query()
            ->where('product_id', $product->id)
            ->findOrFail($imageId);

        $validated = $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $shouldBePrimary = $request->boolean('is_primary');
        if ($shouldBePrimary) {
            $this->clearPrimaryFlags($product, $image->id);
        }

        $data = [
            'alt_text' => $validated['alt_text'] ?? null,
            'order' => (int) ($validated['order'] ?? $image->order),
            'is_primary' => $shouldBePrimary,
        ];

        if ($request->hasFile('image')) {
            if ($image->image) {
                Storage::disk('public')->delete($image->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $image->update($data);
        $this->ensurePrimaryImage($product);

        return back()->with('success', 'Product image updated successfully.');
    }

    public function destroy(Product $product, int $imageId)
    {
        $image = ProductImage::query()
            ->where('product_id', $product->id)
            ->findOrFail($imageId);

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $this->ensurePrimaryImage($product);
        }

        return back()->with('success', 'Product image moved to trash.');
    }

    public function restore(Product $product, int $imageId)
    {
        $image = ProductImage::onlyTrashed()
            ->where('product_id', $product->id)
            ->findOrFail($imageId);

        $hasPrimary = ProductImage::query()
            ->where('product_id', $product->id)
            ->where('is_primary', true)
            ->exists();

        $image->restore();

        if ($hasPrimary) {
            if ($image->is_primary) {
                $image->update(['is_primary' => false]);
            }
        } else {
            $this->clearPrimaryFlags($product);
            $image->update(['is_primary' => true]);
        }

        return back()->with('success', 'Product image restored successfully.');
    }

    public function forceDestroy(Product $product, int $imageId)
    {
        $image = ProductImage::onlyTrashed()
            ->where('product_id', $product->id)
            ->findOrFail($imageId);

        if ($image->image) {
            Storage::disk('public')->delete($image->image);
        }

        $image->forceDelete();
        $this->ensurePrimaryImage($product);

        return back()->with('success', 'Product image permanently deleted.');
    }

    private function clearPrimaryFlags(Product $product, ?int $exceptImageId = null): void
    {
        $query = ProductImage::query()->where('product_id', $product->id);

        if ($exceptImageId !== null) {
            $query->where('id', '!=', $exceptImageId);
        }

        $query->update(['is_primary' => false]);
    }

    private function ensurePrimaryImage(Product $product): void
    {
        $hasPrimary = ProductImage::query()
            ->where('product_id', $product->id)
            ->where('is_primary', true)
            ->exists();

        if ($hasPrimary) {
            return;
        }

        $fallback = ProductImage::query()
            ->where('product_id', $product->id)
            ->orderBy('order')
            ->orderBy('id')
            ->first();

        if ($fallback) {
            $fallback->update(['is_primary' => true]);
        }
    }
}
