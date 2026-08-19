<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $fashionVendor = Vendor::where('shop_name', 'Fashion Hub BD')->first();
        $techVendor = Vendor::where('shop_name', 'Tech Galaxy')->first();
        $homeVendor = Vendor::where('shop_name', 'Home Essentials')->first();

        if (!$fashionVendor || !$techVendor || !$homeVendor) {
            return;
        }

        // Fashion Products
        $fashionCategory = Category::where('name', "Men's Fashion")->first();
        $mobileCategory = Category::where('name', 'Mobile Phones')->first();
        $laptopCategory = Category::where('name', 'Laptops & Computers')->first();
        $furnitureCategory = Category::where('name', 'Furniture')->first();
        $kitchenCategory = Category::where('name', 'Kitchen & Dining')->first();
        $skincareCategory = Category::where('name', 'Skincare')->first();
        $sportsEquipmentCategory = Category::where('name', 'Sports Equipment')->first();
        $booksCategory = Category::where('name', 'Books')->first();

        if (
            !$fashionCategory ||
            !$mobileCategory ||
            !$laptopCategory ||
            !$furnitureCategory ||
            !$kitchenCategory
        ) {
            return;
        }

        $fashionProducts = [
            [
                'name' => 'Premium Cotton T-Shirt',
                'short_description' => 'Comfortable 100% cotton t-shirt for everyday wear',
                'description' => '<p>Experience ultimate comfort with our premium cotton t-shirt. Made from 100% organic cotton, this t-shirt offers breathability and durability.</p><ul><li>100% Organic Cotton</li><li>Pre-shrunk fabric</li><li>Reinforced stitching</li><li>Available in multiple colors</li></ul>',
                'price' => 599,
                'compare_price' => 899,
                'quantity' => 150,
                'featured' => true,
            ],
            [
                'name' => 'Classic Denim Jeans',
                'short_description' => 'Stylish slim-fit denim jeans for modern look',
                'description' => '<p>Our classic denim jeans combine style with comfort. Perfect for casual and semi-formal occasions.</p>',
                'price' => 1499,
                'compare_price' => 2199,
                'quantity' => 80,
                'featured' => true,
            ],
            [
                'name' => 'Formal Button-Down Shirt',
                'short_description' => 'Elegant formal shirt for office and occasions',
                'description' => '<p>Look sharp and professional with our formal button-down shirt. Made from premium cotton blend fabric.</p>',
                'price' => 1299,
                'compare_price' => 1699,
                'quantity' => 60,
                'featured' => false,
            ],
            [
                'name' => 'Winter Hoodie Jacket',
                'short_description' => 'Warm and cozy hoodie for winter season',
                'description' => '<p>Stay warm and stylish with our winter hoodie jacket. Features fleece lining and adjustable hood.</p>',
                'price' => 1899,
                'compare_price' => 2499,
                'quantity' => 45,
                'featured' => true,
            ],
        ];

        foreach ($fashionProducts as $productData) {
            $brandId = Brand::query()->inRandomOrder()->value('id');
            if (!$brandId) {
                continue;
            }

            $image = match ($productData['name']) {
                'Premium Cotton T-Shirt' => 'images/products/cotton-tshirt.jpg',
                'Classic Denim Jeans' => 'images/products/denim-jeans.jpg',
                'Formal Button-Down Shirt' => 'images/products/formal-shirt.jpg',
                'Winter Hoodie Jacket' => 'images/products/winter-hoodie.jpg',
                default => null,
            };

            $product = Product::updateOrCreate([
                'vendor_id' => $fashionVendor->id,
                'name' => $productData['name'],
            ], [
                'vendor_id' => $fashionVendor->id,
                'category_id' => $fashionCategory->id,
                'brand_id' => $brandId,
                ...$productData,
                'status' => 'active',
                'published_at' => now(),
            ]);

            if ($image) {
                ProductImage::updateOrCreate([
                    'product_id' => $product->id,
                    'image' => $image,
                ], [
                    'is_primary' => true,
                    'order' => 0,
                ]);
            }
        }

        // Tech Products
        $techProducts = [
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'short_description' => 'Flagship smartphone with S Pen and AI features',
                'description' => '<p>The Samsung Galaxy S24 Ultra features a stunning 6.8" Dynamic AMOLED display, powerful processor, and advanced AI capabilities.</p><ul><li>6.8" QHD+ Display</li><li>200MP Camera System</li><li>S Pen Included</li><li>5000mAh Battery</li></ul>',
                'price' => 159999,
                'compare_price' => 179999,
                'quantity' => 25,
                'featured' => true,
                'category_id' => $mobileCategory->id,
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'short_description' => 'Apple\'s most advanced iPhone with titanium design',
                'description' => '<p>Experience the power of iPhone 15 Pro Max with A17 Pro chip, titanium design, and advanced camera system.</p>',
                'price' => 189999,
                'compare_price' => 199999,
                'quantity' => 20,
                'featured' => true,
                'category_id' => $mobileCategory->id,
            ],
            [
                'name' => 'Xiaomi 14 Pro',
                'short_description' => 'Premium smartphone with Leica optics',
                'description' => '<p>Xiaomi 14 Pro features Leica professional optics, Snapdragon 8 Gen 3, and 120W HyperCharge.</p>',
                'price' => 89999,
                'compare_price' => 99999,
                'quantity' => 35,
                'featured' => false,
                'category_id' => $mobileCategory->id,
            ],
            [
                'name' => 'MacBook Pro 14" M3 Pro',
                'short_description' => 'Professional laptop with M3 Pro chip',
                'description' => '<p>MacBook Pro 14 inch with M3 Pro chip delivers exceptional performance for professionals.</p>',
                'price' => 289999,
                'compare_price' => null,
                'quantity' => 15,
                'featured' => true,
                'category_id' => $laptopCategory->id,
            ],
            [
                'name' => 'Dell XPS 15 (2024)',
                'short_description' => 'Premium Windows laptop with OLED display',
                'description' => '<p>Dell XPS 15 features stunning 3.5K OLED display, Intel Core Ultra processor, and premium build quality.</p>',
                'price' => 199999,
                'compare_price' => 229999,
                'quantity' => 18,
                'featured' => false,
                'category_id' => $laptopCategory->id,
            ],
        ];

        foreach ($techProducts as $productData) {
            $categoryId = $productData['category_id'];
            unset($productData['category_id']);

            $brandId = Brand::query()->inRandomOrder()->value('id');
            if (!$brandId) {
                continue;
            }

            $image = match ($productData['name']) {
                'Samsung Galaxy S24 Ultra' => 'images/products/samsung-s24.jpg',
                'iPhone 15 Pro Max' => 'images/products/iphone-15.jpg',
                'Xiaomi 14 Pro' => 'images/products/xiaomi-14.jpg',
                'MacBook Pro 14" M3 Pro' => 'images/products/macbook-pro.jpg',
                'Dell XPS 15 (2024)' => 'images/products/dell-xps.jpg',
                default => null,
            };

            $product = Product::updateOrCreate([
                'vendor_id' => $techVendor->id,
                'name' => $productData['name'],
            ], [
                'vendor_id' => $techVendor->id,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                ...$productData,
                'status' => 'active',
                'published_at' => now(),
            ]);

            if ($image) {
                ProductImage::updateOrCreate([
                    'product_id' => $product->id,
                    'image' => $image,
                ], [
                    'is_primary' => true,
                    'order' => 0,
                ]);
            }
        }

        // Home Products
        $homeProducts = [
            [
                'name' => 'Modern L-Shape Sofa',
                'short_description' => 'Elegant L-shape sofa for your living room',
                'description' => '<p>Transform your living room with our modern L-shape sofa. Premium fabric upholstery with foam cushioning for maximum comfort.</p>',
                'price' => 45999,
                'compare_price' => 59999,
                'quantity' => 8,
                'featured' => true,
                'category_id' => $furnitureCategory->id,
            ],
            [
                'name' => 'Wooden Dining Table Set (6 Seater)',
                'short_description' => '6-seater dining table with chairs',
                'description' => '<p>Solid wood dining table set with 6 chairs. Perfect for family dinners and gatherings.</p>',
                'price' => 35999,
                'compare_price' => 42999,
                'quantity' => 12,
                'featured' => true,
                'category_id' => $furnitureCategory->id,
            ],
            [
                'name' => 'Non-Stick Cookware Set (10 Pcs)',
                'short_description' => 'Complete non-stick cookware set for your kitchen',
                'description' => '<p>Premium quality non-stick cookware set includes frying pans, sauce pans, and cooking utensils.</p>',
                'price' => 4999,
                'compare_price' => 6999,
                'quantity' => 50,
                'featured' => false,
                'category_id' => $kitchenCategory->id,
            ],
            [
                'name' => 'Smart Rice Cooker 2.8L',
                'short_description' => 'Digital rice cooker with multiple cooking modes',
                'description' => '<p>Cook perfect rice every time with our smart rice cooker. Features keep-warm function and timer.</p>',
                'price' => 3499,
                'compare_price' => 4299,
                'quantity' => 40,
                'featured' => false,
                'category_id' => $kitchenCategory->id,
            ],
        ];

        foreach ($homeProducts as $productData) {
            $categoryId = $productData['category_id'];
            unset($productData['category_id']);

            $brandId = Brand::query()->inRandomOrder()->value('id');
            if (!$brandId) {
                continue;
            }

            $image = match ($productData['name']) {
                'Modern L-Shape Sofa' => 'images/products/l-shape-sofa.jpg',
                'Wooden Dining Table Set (6 Seater)' => 'images/products/dining-table.jpg',
                'Non-Stick Cookware Set (10 Pcs)' => 'images/products/cookware-set.jpg',
                'Smart Rice Cooker 2.8L' => 'images/products/rice-cooker.jpg',
                default => null,
            };

            $product = Product::updateOrCreate([
                'vendor_id' => $homeVendor->id,
                'name' => $productData['name'],
            ], [
                'vendor_id' => $homeVendor->id,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                ...$productData,
                'status' => 'active',
                'published_at' => now(),
            ]);

            if ($image) {
                ProductImage::updateOrCreate([
                    'product_id' => $product->id,
                    'image' => $image,
                ], [
                    'is_primary' => true,
                    'order' => 0,
                ]);
            }
        }

        // Additional category coverage for Beauty, Sports and Books
        $additionalProducts = [
            [
                'vendor_id' => $fashionVendor->id,
                'category_id' => $skincareCategory?->id,
                'name' => 'Hydrating Aloe Vera Face Wash',
                'short_description' => 'Gentle daily cleanser with aloe vera and niacinamide',
                'description' => '<p>A refreshing face wash that removes dirt and excess oil without drying your skin.</p>',
                'price' => 699,
                'compare_price' => 899,
                'quantity' => 90,
                'featured' => false,
            ],
            [
                'vendor_id' => $fashionVendor->id,
                'category_id' => $skincareCategory?->id,
                'name' => 'Vitamin C Brightening Serum',
                'short_description' => '10% vitamin C serum for glow and dark spot care',
                'description' => '<p>Lightweight antioxidant serum designed to brighten and even skin tone.</p>',
                'price' => 1299,
                'compare_price' => 1599,
                'quantity' => 70,
                'featured' => true,
            ],
            [
                'vendor_id' => $techVendor->id,
                'category_id' => $sportsEquipmentCategory?->id,
                'name' => 'Professional Yoga Mat 6mm',
                'short_description' => 'Non-slip yoga mat with extra cushioning',
                'description' => '<p>Premium quality yoga mat suitable for daily workout, stretching and meditation.</p>',
                'price' => 1499,
                'compare_price' => 1899,
                'quantity' => 65,
                'featured' => false,
            ],
            [
                'vendor_id' => $techVendor->id,
                'category_id' => $sportsEquipmentCategory?->id,
                'name' => 'Adjustable Dumbbell Pair 20KG',
                'short_description' => 'Compact adjustable dumbbells for home gym',
                'description' => '<p>Save space and train effectively with an adjustable dumbbell set up to 20KG.</p>',
                'price' => 7999,
                'compare_price' => 9499,
                'quantity' => 25,
                'featured' => true,
            ],
            [
                'vendor_id' => $homeVendor->id,
                'category_id' => $booksCategory?->id,
                'name' => 'Atomic Habits (Paperback)',
                'short_description' => 'Bestselling self-improvement book',
                'description' => '<p>A practical guide to building good habits and breaking bad ones.</p>',
                'price' => 450,
                'compare_price' => 550,
                'quantity' => 110,
                'featured' => false,
            ],
            [
                'vendor_id' => $homeVendor->id,
                'category_id' => $booksCategory?->id,
                'name' => 'Premium A4 Notebook Set (Pack of 5)',
                'short_description' => 'Smooth paper notebooks for office and study',
                'description' => '<p>Durable notebook set with premium quality paper for writing and sketching.</p>',
                'price' => 799,
                'compare_price' => 999,
                'quantity' => 140,
                'featured' => false,
            ],
        ];

        foreach ($additionalProducts as $productData) {
            if (empty($productData['category_id'])) {
                continue;
            }

            $brandId = Brand::query()->inRandomOrder()->value('id');
            if (!$brandId) {
                continue;
            }

            $image = match ($productData['name']) {
                'Hydrating Aloe Vera Face Wash' => 'images/products/aloe-face-wash.jpg',
                'Vitamin C Brightening Serum' => 'images/products/vitamin-c-serum.jpg',
                'Professional Yoga Mat 6mm' => 'images/products/yoga-mat.jpg',
                'Adjustable Dumbbell Pair 20KG' => 'images/products/adjustable-dumbbell.jpg',
                'Atomic Habits (Paperback)' => 'images/products/atomic-habits.jpg',
                'Premium A4 Notebook Set (Pack of 5)' => 'images/products/a4-notebook.jpg',
                default => null,
            };

            $product = Product::updateOrCreate(
                [
                    'vendor_id' => $productData['vendor_id'],
                    'name' => $productData['name'],
                ],
                [
                    'vendor_id' => $productData['vendor_id'],
                    'category_id' => $productData['category_id'],
                    'brand_id' => $brandId,
                    'name' => $productData['name'],
                    'short_description' => $productData['short_description'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'compare_price' => $productData['compare_price'],
                    'quantity' => $productData['quantity'],
                    'featured' => $productData['featured'],
                    'status' => 'active',
                    'published_at' => now(),
                ]
            );

            if ($image) {
                ProductImage::updateOrCreate([
                    'product_id' => $product->id,
                    'image' => $image,
                ], [
                    'is_primary' => true,
                    'order' => 0,
                ]);
            }
        }
    }
}
