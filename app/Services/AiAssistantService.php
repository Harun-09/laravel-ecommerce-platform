<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Product;
use Illuminate\Support\Str;

class AiAssistantService
{
    public function generateAnswer(Product $product, string $question): string
    {
        $questionLower = Str::lower($question);
        
        // Load relationships if not loaded
        $product->loadMissing([
            'reviews',
            'brand',
            'category',
            'vendor',
            'variations.attributes.attribute',
            'variations.attributes.value',
        ]);

        // Basic Info
        $name = $product->name;
        $price = store_money($product->final_price);
        $brand = $product->brand?->name ?? 'N/A';
        $category = $product->category?->name ?? 'N/A';
        $description = strip_tags($product->description);
        $shortDescription = strip_tags($product->short_description);
        $variationSpecs = $product->variations
            ->flatMap(fn($variation) => $variation->attributes)
            ->map(function ($attribute) {
                $attributeName = $attribute->attribute?->name;
                $attributeValue = $attribute->value?->value;

                return ($attributeName && $attributeValue) ? "{$attributeName}: {$attributeValue}" : null;
            })
            ->filter()
            ->unique()
            ->take(6)
            ->values();
        
        // Stock / Delivery logic
        if (Str::contains($questionLower, ['stock', 'available', 'availability', 'how many'])) {
            $stock = $product->quantity > 0 ? "In stock ({$product->quantity} units available)." : "Out of stock.";
            if (!$product->track_quantity) $stock = "In stock and ready to ship.";
            return "For the {$name}, it is currently {$stock} This item is sold by " . ($product->vendor?->shop_name ?? 'NovaMart') . ".";
        }

        // Price logic
        if (Str::contains($questionLower, ['price', 'cost', 'how much', 'discount', 'save'])) {
            $priceMsg = "The current price for {$name} is {$price}.";
            if ($product->compare_price > $product->price) {
                $saved = store_money($product->compare_price - $product->price);
                $priceMsg .= " You save {$saved} compared to the regular price.";
            }
            return $priceMsg;
        }

        // Feature / Quality logic from reviews
        if (Str::contains($questionLower, ['review', 'customer', 'people say', 'quality', 'good', 'bad', 'rating'])) {
            $avgRating = number_format($product->reviews()->avg('rating') ?? 0, 1);
            $count = $product->reviews()->count();
            if ($count > 0) {
                $latestReview = $product->reviews()->latest()->first();
                return "The {$name} has a rating of {$avgRating}/5 from {$count} customers. One recent shopper mentioned: \"{$latestReview->comment}\"";
            }
            return "The {$name} is a high-quality selection in our {$category} category. While we don't have many reviews yet, it's a popular choice for its reliability.";
        }

        // Technical Specs / Features logic
        if (Str::contains($questionLower, ['feature', 'spec', 'detail', 'what is', 'tell me about'])) {
            if ($variationSpecs->isNotEmpty()) {
                $specSummary = $variationSpecs->implode(', ');

                return "The {$name} from {$brand} includes these key specs: {$specSummary}. It's a strong option in the {$category} category.";
            }

            $features = collect(explode('.', $shortDescription . ' ' . $description))
                ->map(fn($s) => trim($s))
                ->filter(fn($s) => strlen($s) > 20)
                ->take(2)
                ->implode('. ');
            
            return "The {$name} from {$brand} is designed for excellence. Key features include: {$features}. It's a top-tier choice in the {$category} section.";
        }

        // Fallback - Natural sounding summary
        return "The {$name} is part of our premium {$category} collection by {$brand}. It currently retails for {$price} and is highly rated for its performance and value. Please check the detailed specifications below for more specifics!";
    }
}
