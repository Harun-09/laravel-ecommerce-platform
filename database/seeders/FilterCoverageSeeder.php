<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FilterCoverageSeeder extends Seeder
{
    public function run(): void
    {
        $vendorByShop = Vendor::query()->get()->keyBy('shop_name');
        $fallbackVendor = $vendorByShop->first() ?? Vendor::query()->first();
        if (!$fallbackVendor) {
            return;
        }

        $brandByName = Brand::query()
            ->active()
            ->get()
            ->keyBy(fn(Brand $brand) => mb_strtolower((string) $brand->name));

        $fallbackBrandId = Brand::query()->active()->value('id');
        $fallbackCategoryId = Category::query()->whereNotNull('parent_id')->value('id')
            ?? Category::query()->value('id');

        if (!$fallbackBrandId || !$fallbackCategoryId) {
            return;
        }

        $findBrandId = function (string $name) use ($brandByName, $fallbackBrandId): int {
            return (int) ($brandByName->get(mb_strtolower($name))?->id ?? $fallbackBrandId);
        };

        $findCategoryId = function (string $parentSlug, ?string $childSlug = null) use ($fallbackCategoryId): int {
            $parent = Category::query()->where('slug', $parentSlug)->first();
            if (!$parent) {
                return (int) $fallbackCategoryId;
            }

            if ($childSlug) {
                $child = Category::query()
                    ->where('parent_id', $parent->id)
                    ->where('slug', $childSlug)
                    ->first();

                if ($child) {
                    return (int) $child->id;
                }
            }

            $firstChildId = Category::query()->where('parent_id', $parent->id)->value('id');
            return (int) ($firstChildId ?: $parent->id);
        };

        $createOrUpdateProduct = function (array $data) use ($vendorByShop, $fallbackVendor, $findBrandId, $findCategoryId): void {
            $vendor = $vendorByShop->get($data['vendor_shop']) ?? $fallbackVendor;
            $brandId = $findBrandId($data['brand_name']);
            $categoryId = $findCategoryId($data['parent_slug'], $data['child_slug'] ?? null);

            $product = Product::updateOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'name' => $data['name'],
                ],
                [
                    'vendor_id' => $vendor->id,
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'name' => $data['name'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'compare_price' => $data['compare_price'] ?? null,
                    'quantity' => $data['quantity'] ?? 50,
                    'featured' => $data['featured'] ?? false,
                    'status' => 'active',
                    'rating' => $data['rating'],
                    'reviews_count' => $data['reviews_count'],
                    'sales_count' => $data['sales_count'],
                    'published_at' => now(),
                ]
            );

            $imagePath = 'images/products/' . $data['parent_slug'] . '/' . Str::slug($data['name']) . '.jpg';
            if (File::exists(public_path($imagePath))) {
                ProductImage::updateOrCreate([
                    'product_id' => $product->id,
                    'is_primary' => true,
                ], [
                    'image' => $imagePath,
                    'order' => 0,
                ]);
            }
        };

        $coverageProducts = [
            [
                'name' => 'Samsung Galaxy A55 5G',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'mobile-phones',
                'brand_name' => 'Samsung',
                'short_description' => 'Balanced performance smartphone with AMOLED display',
                'description' => '<p>Samsung Galaxy A55 offers a vivid AMOLED display, long battery life and dependable camera performance.</p>',
                'price' => 54999,
                'compare_price' => 59999,
                'rating' => 4.6,
                'reviews_count' => 124,
                'sales_count' => 210,
                'featured' => true,
            ],
            [
                'name' => 'Apple iPhone 15',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'mobile-phones',
                'brand_name' => 'Apple',
                'short_description' => 'Premium iPhone with advanced camera and A16 chip',
                'description' => '<p>iPhone 15 combines excellent battery life, camera quality and smooth software performance.</p>',
                'price' => 139999,
                'compare_price' => 149999,
                'rating' => 4.8,
                'reviews_count' => 196,
                'sales_count' => 340,
                'featured' => true,
            ],
            [
                'name' => 'Xiaomi Redmi Note 13 Pro',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'mobile-phones',
                'brand_name' => 'Xiaomi',
                'short_description' => 'Value-packed smartphone with high refresh display',
                'description' => '<p>Redmi Note 13 Pro delivers top value with strong battery backup and smooth performance.</p>',
                'price' => 36999,
                'compare_price' => 41999,
                'rating' => 4.2,
                'reviews_count' => 88,
                'sales_count' => 165,
            ],
            [
                'name' => 'OnePlus Nord 4',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'mobile-phones',
                'brand_name' => 'OnePlus',
                'short_description' => 'Fast and clean Android experience for daily use',
                'description' => '<p>OnePlus Nord 4 is optimized for smooth app performance and fast charging.</p>',
                'price' => 48999,
                'compare_price' => 53999,
                'rating' => 4.1,
                'reviews_count' => 74,
                'sales_count' => 140,
            ],
            [
                'name' => 'Sony WH-CH720N Headphones',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'accessories',
                'brand_name' => 'Sony',
                'short_description' => 'Wireless noise-canceling headphones for clear audio',
                'description' => '<p>Enjoy immersive music and calls with Sony WH-CH720N noise-canceling headphones.</p>',
                'price' => 18999,
                'compare_price' => 21999,
                'rating' => 4.4,
                'reviews_count' => 63,
                'sales_count' => 119,
            ],
            [
                'name' => 'LG Smart Inverter Refrigerator 308L',
                'vendor_shop' => 'Home Essentials',
                'parent_slug' => 'home-living',
                'child_slug' => 'kitchen-dining',
                'brand_name' => 'LG',
                'short_description' => 'Energy efficient refrigerator with smart inverter compressor',
                'description' => '<p>LG inverter refrigerator keeps food fresher for longer while reducing power consumption.</p>',
                'price' => 78999,
                'compare_price' => 85999,
                'rating' => 3.8,
                'reviews_count' => 41,
                'sales_count' => 57,
            ],
            [
                'name' => 'HP LaserJet Pro MFP',
                'vendor_shop' => 'Home Essentials',
                'parent_slug' => 'books-stationery',
                'child_slug' => 'office-supplies',
                'brand_name' => 'HP',
                'short_description' => 'Reliable multifunction printer for office and home',
                'description' => '<p>Print, scan and copy efficiently with the HP LaserJet Pro multifunction printer.</p>',
                'price' => 32999,
                'compare_price' => 36999,
                'rating' => 3.4,
                'reviews_count' => 39,
                'sales_count' => 48,
            ],
            [
                'name' => 'Dell Inspiron 15 Laptop',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'laptops-computers',
                'brand_name' => 'Dell',
                'short_description' => 'Everyday productivity laptop with full HD display',
                'description' => '<p>Dell Inspiron 15 is ideal for study and office tasks with smooth multitasking performance.</p>',
                'price' => 75999,
                'compare_price' => 82999,
                'rating' => 3.9,
                'reviews_count' => 52,
                'sales_count' => 92,
            ],
            [
                'name' => 'Lenovo IdeaPad Slim 5',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'laptops-computers',
                'brand_name' => 'Lenovo',
                'short_description' => 'Slim and lightweight laptop for daily productivity',
                'description' => '<p>Lenovo IdeaPad Slim 5 provides dependable performance and portability.</p>',
                'price' => 67999,
                'compare_price' => 74999,
                'rating' => 2.8,
                'reviews_count' => 33,
                'sales_count' => 61,
            ],
            [
                'name' => 'Asus Vivobook 15 OLED',
                'vendor_shop' => 'Tech Galaxy',
                'parent_slug' => 'electronics',
                'child_slug' => 'laptops-computers',
                'brand_name' => 'Asus',
                'short_description' => 'OLED display laptop for vivid visuals and speed',
                'description' => '<p>Asus Vivobook 15 OLED combines excellent visuals and everyday productivity.</p>',
                'price' => 73999,
                'compare_price' => 80999,
                'rating' => 2.3,
                'reviews_count' => 27,
                'sales_count' => 45,
            ],
            [
                'name' => 'Premium Men Polo Shirt',
                'vendor_shop' => 'Fashion Hub BD',
                'parent_slug' => 'fashion',
                'child_slug' => 'mens-fashion',
                'brand_name' => 'Nike',
                'short_description' => 'Comfortable and breathable polo shirt for daily wear',
                'description' => '<p>Classic fit polo shirt made for comfort and modern style.</p>',
                'price' => 1199,
                'compare_price' => 1499,
                'rating' => 4.0,
                'reviews_count' => 86,
                'sales_count' => 178,
            ],
            [
                'name' => 'Vitamin C Glow Serum',
                'vendor_shop' => 'Fashion Hub BD',
                'parent_slug' => 'beauty-health',
                'child_slug' => 'skincare',
                'brand_name' => 'Aarong',
                'short_description' => 'Daily skin brightening serum with vitamin C',
                'description' => '<p>A lightweight serum that helps improve skin brightness and texture.</p>',
                'price' => 999,
                'compare_price' => 1299,
                'rating' => 3.7,
                'reviews_count' => 58,
                'sales_count' => 132,
            ],
            [
                'name' => 'Adjustable Dumbbell Set 20KG',
                'vendor_shop' => 'Fashion Hub BD',
                'parent_slug' => 'sports-outdoors',
                'child_slug' => 'sports-equipment',
                'brand_name' => 'Adidas',
                'short_description' => 'Space-saving adjustable dumbbell set for home workout',
                'description' => '<p>Build strength at home with a compact and durable adjustable dumbbell set.</p>',
                'price' => 8499,
                'compare_price' => 9999,
                'rating' => 3.3,
                'reviews_count' => 44,
                'sales_count' => 76,
            ],
            [
                'name' => 'Organic Basmati Rice 5KG',
                'vendor_shop' => 'Home Essentials',
                'parent_slug' => 'groceries',
                'child_slug' => 'food-beverages',
                'brand_name' => 'Apex',
                'short_description' => 'Premium long grain basmati rice for everyday cooking',
                'description' => '<p>Fresh and aromatic basmati rice, ideal for pulao, biryani and daily meals.</p>',
                'price' => 1250,
                'compare_price' => 1450,
                'rating' => 2.6,
                'reviews_count' => 31,
                'sales_count' => 59,
            ],
        ];

        foreach ($coverageProducts as $productData) {
            $createOrUpdateProduct($productData);
        }

        $catalogExpansionBlueprints = [
            [
                'parent_slug' => 'fashion',
                'child_slug' => 'mens-fashion',
                'vendor_shop' => 'Fashion Hub BD',
                'brand_names' => ['Nike', 'Adidas', 'Puma', 'Sailor', 'Richman'],
                'base_price' => 899,
                'price_step' => 140,
                'description_seed' => 'Comfort-focused daily wear piece built with durable stitching.',
                'keywords' => [
                    'Classic Cotton Polo',
                    'Slim Fit Oxford Shirt',
                    'Breathable Crew Neck Tee',
                    'Linen Casual Shirt',
                    'Stretch Chino Pant',
                    'Everyday Denim Jacket',
                    'Zip Front Hoodie',
                    'Formal Office Trouser',
                    'Soft Knit Henley Tee',
                    'Relaxed Fit Jogger Pant',
                    'Premium Flannel Shirt',
                    'Weekend Casual Blazer',
                ],
            ],
            [
                'parent_slug' => 'home-living',
                'child_slug' => 'furniture',
                'vendor_shop' => 'Home Essentials',
                'brand_names' => ['Apex', 'LG', 'Yellow', 'Raymond'],
                'base_price' => 5999,
                'price_step' => 1400,
                'description_seed' => 'Built for home usability with practical finish and long-term durability.',
                'keywords' => [
                    'Compact TV Cabinet',
                    'Modern Coffee Table',
                    'Queen Size Bed Frame',
                    'Ergonomic Office Chair',
                    'Minimalist Bookshelf',
                    'Multi-Drawer Storage Unit',
                    'Wooden Shoe Rack',
                    'Corner Display Shelf',
                    'Folding Study Desk',
                    'Lounge Accent Chair',
                ],
            ],
            [
                'parent_slug' => 'home-living',
                'child_slug' => 'kitchen-dining',
                'vendor_shop' => 'Home Essentials',
                'brand_names' => ['Apex', 'LG', 'Yellow', 'Samsung'],
                'base_price' => 1299,
                'price_step' => 520,
                'description_seed' => 'Reliable kitchen companion for daily meal prep and serving.',
                'keywords' => [
                    'Stainless Steel Knife Set',
                    'Premium Pressure Cooker',
                    'Electric Kettle 1.8L',
                    'Induction Friendly Fry Pan',
                    'Glass Food Storage Set',
                    'Ceramic Dinner Plate Set',
                    'Multi-Layer Lunch Box',
                    'Non-Slip Chopping Board',
                    'Silicone Baking Tool Kit',
                    'Thermal Water Flask',
                ],
            ],
            [
                'parent_slug' => 'beauty-health',
                'child_slug' => 'skincare',
                'vendor_shop' => 'Fashion Hub BD',
                'brand_names' => ['Aarong', 'Nike', 'Puma', 'Adidas'],
                'base_price' => 649,
                'price_step' => 180,
                'description_seed' => 'Formulated for visible skin-care results with daily use.',
                'keywords' => [
                    'Hydrating Gel Moisturizer',
                    'Niacinamide Repair Serum',
                    'Daily SPF 50 Sunscreen',
                    'Gentle Foaming Cleanser',
                    'Overnight Renewal Cream',
                    'Vitamin E Body Lotion',
                    'Brightening Face Toner',
                    'Clay Detox Face Mask',
                    'Aloe Vera Soothing Gel',
                    'Deep Hydration Eye Cream',
                ],
            ],
            [
                'parent_slug' => 'sports-outdoors',
                'child_slug' => 'sports-equipment',
                'vendor_shop' => 'Tech Galaxy',
                'brand_names' => ['Adidas', 'Nike', 'Puma', 'Asus'],
                'base_price' => 1799,
                'price_step' => 680,
                'description_seed' => 'Performance-ready equipment suitable for routine training.',
                'keywords' => [
                    'Resistance Band Kit',
                    'Kettlebell Training Set',
                    'Heavy Jump Rope',
                    'Ab Roller Wheel',
                    'Push Up Bar Pair',
                    'Foam Roller Pro',
                    'Grip Strength Trainer',
                    'Wrist Ankle Weight Set',
                    'Pull Up Assist Band',
                    'Home Gym Bench',
                ],
            ],
            [
                'parent_slug' => 'books-stationery',
                'child_slug' => 'office-supplies',
                'vendor_shop' => 'Home Essentials',
                'brand_names' => ['HP', 'Dell', 'Lenovo', 'Apex'],
                'base_price' => 299,
                'price_step' => 210,
                'description_seed' => 'Office-ready item designed for smooth daily productivity.',
                'keywords' => [
                    'Premium Ball Pen Pack',
                    'A4 Printing Paper Box',
                    'Desk Organizer Tray',
                    'Wirebound Meeting Notebook',
                    'Sticky Note Combo Pack',
                    'Mechanical Pencil Set',
                    'Clear File Folder Bundle',
                    'Permanent Marker Set',
                    'Desktop Calculator',
                    'Stapler With Pins Kit',
                ],
            ],
            [
                'parent_slug' => 'groceries',
                'child_slug' => 'food-beverages',
                'vendor_shop' => 'Home Essentials',
                'brand_names' => ['Apex', 'Yellow', 'LG', 'Samsung'],
                'base_price' => 220,
                'price_step' => 90,
                'description_seed' => 'Fresh pantry essential selected for consistent taste and quality.',
                'keywords' => [
                    'Premium Atta Flour 2KG',
                    'Fresh Mustard Oil 1L',
                    'Organic Red Lentil 1KG',
                    'Pure Honey Jar 500G',
                    'Roasted Peanut Mix 500G',
                    'Breakfast Oats 1KG',
                    'Natural Apple Vinegar 500ML',
                    'Whole Spice Combo Pack',
                    'Instant Noodles Family Pack',
                    'Classic Tea Leaves 500G',
                    'Brown Sugar Pack 1KG',
                    'Premium Semolina 1KG',
                ],
            ],
        ];

        foreach ($catalogExpansionBlueprints as $blueprintIndex => $blueprint) {
            $brandNames = collect($blueprint['brand_names'] ?? [])->filter()->values();
            if ($brandNames->isEmpty()) {
                $brandNames = collect(['Samsung']);
            }

            $basePrice = (int) ($blueprint['base_price'] ?? 999);
            $priceStep = (int) ($blueprint['price_step'] ?? 120);
            $descriptionSeed = trim((string) ($blueprint['description_seed'] ?? 'Reliable product for daily use.'));
            $keywords = collect($blueprint['keywords'] ?? [])->map(fn($value) => trim((string) $value))->filter()->values();

            foreach ($keywords as $itemIndex => $keyword) {
                $brandName = (string) $brandNames->get($itemIndex % $brandNames->count(), 'Samsung');
                $price = $basePrice + ($itemIndex * $priceStep);
                $comparePrice = $price + max(120, (int) round($price * 0.18));
                $rating = min(4.9, 3.6 + (($itemIndex % 4) * 0.3));
                $reviewsCount = 28 + ($blueprintIndex * 7) + ($itemIndex * 3);
                $salesCount = 45 + ($blueprintIndex * 11) + ($itemIndex * 5);
                $shortDescription = Str::limit($keyword . ' for dependable everyday use and long-lasting quality', 95, '');

                $createOrUpdateProduct([
                    'name' => $keyword,
                    'vendor_shop' => (string) ($blueprint['vendor_shop'] ?? 'Home Essentials'),
                    'parent_slug' => (string) ($blueprint['parent_slug'] ?? 'electronics'),
                    'child_slug' => $blueprint['child_slug'] ?? null,
                    'brand_name' => $brandName,
                    'short_description' => $shortDescription,
                    'description' => '<p>' . e($keyword) . ' by ' . e($brandName) . '. ' . e($descriptionSeed) . '</p>',
                    'price' => $price,
                    'compare_price' => $comparePrice,
                    'quantity' => 40 + ($itemIndex % 5) * 15,
                    'rating' => $rating,
                    'reviews_count' => $reviewsCount,
                    'sales_count' => $salesCount,
                    'featured' => $itemIndex % 5 === 0,
                ]);
            }
        }

        $topBrandNames = ['Samsung', 'Apple', 'Xiaomi', 'OnePlus', 'Sony', 'LG', 'HP', 'Dell', 'Lenovo', 'Asus'];
        $topCategorySlugs = ['electronics', 'fashion', 'home-living', 'beauty-health', 'sports-outdoors', 'books-stationery', 'groceries'];

        foreach ($topBrandNames as $index => $brandName) {
            $brandId = $findBrandId($brandName);
            $exists = Product::query()
                ->where('status', 'active')
                ->where('brand_id', $brandId)
                ->exists();

            if ($exists) {
                continue;
            }

            $createOrUpdateProduct([
                'name' => $brandName . ' Featured Product',
                'vendor_shop' => $index % 2 === 0 ? 'Tech Galaxy' : 'Home Essentials',
                'parent_slug' => 'electronics',
                'child_slug' => 'mobile-phones',
                'brand_name' => $brandName,
                'short_description' => $brandName . ' quality product for filter coverage',
                'description' => '<p>Auto-generated product to ensure brand filter coverage.</p>',
                'price' => 1999 + ($index * 350),
                'compare_price' => 2499 + ($index * 350),
                'rating' => 4.0,
                'reviews_count' => 20 + ($index * 3),
                'sales_count' => 40 + ($index * 4),
            ]);
        }

        foreach ($topCategorySlugs as $index => $parentSlug) {
            $parent = Category::query()->where('slug', $parentSlug)->first();
            if (!$parent) {
                continue;
            }

            $categoryIds = Category::query()
                ->where('id', $parent->id)
                ->orWhere('parent_id', $parent->id)
                ->pluck('id');

            $exists = Product::query()
                ->where('status', 'active')
                ->whereIn('category_id', $categoryIds)
                ->exists();

            if ($exists) {
                continue;
            }

            $readable = Str::title(str_replace('-', ' ', $parentSlug));
            $createOrUpdateProduct([
                'name' => $readable . ' Starter Product',
                'vendor_shop' => $index % 2 === 0 ? 'Home Essentials' : 'Fashion Hub BD',
                'parent_slug' => $parentSlug,
                'brand_name' => 'Samsung',
                'short_description' => 'Starter product for ' . $readable . ' filter coverage',
                'description' => '<p>Auto-generated product to ensure category filter coverage.</p>',
                'price' => 999 + ($index * 300),
                'compare_price' => 1299 + ($index * 300),
                'rating' => 3.5,
                'reviews_count' => 18 + ($index * 2),
                'sales_count' => 30 + ($index * 3),
            ]);
        }

        Product::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->chunkById(200, function ($products): void {
                foreach ($products as $product) {
                    $updates = [];

                    if ((float) $product->rating <= 0) {
                        $updates['rating'] = match ($product->id % 4) {
                            0 => 4.5,
                            1 => 4.1,
                            2 => 3.3,
                            default => 2.4,
                        };
                    }

                    if ((int) $product->reviews_count <= 0) {
                        $updates['reviews_count'] = 12 + ($product->id % 70);
                    }

                    if ((int) $product->sales_count <= 0) {
                        $updates['sales_count'] = 8 + ($product->id % 120);
                    }

                    if (!$product->published_at) {
                        $updates['published_at'] = now()->subDays($product->id % 30);
                    }

                    if ($updates !== []) {
                        $product->update($updates);
                    }
                }
            });
    }
}
