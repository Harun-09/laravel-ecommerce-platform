<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'icon' => 'fas fa-laptop',
                'children' => [
                    ['name' => 'Mobile Phones', 'icon' => 'fas fa-mobile-alt'],
                    ['name' => 'Laptops & Computers', 'icon' => 'fas fa-laptop'],
                    ['name' => 'Tablets', 'icon' => 'fas fa-tablet-alt'],
                    ['name' => 'Cameras', 'icon' => 'fas fa-camera'],
                    ['name' => 'Accessories', 'icon' => 'fas fa-headphones'],
                ],
            ],
            [
                'name' => 'Fashion',
                'icon' => 'fas fa-tshirt',
                'featured' => true,
                'children' => [
                    ['name' => "Men's Fashion", 'icon' => 'fas fa-male'],
                    ['name' => "Women's Fashion", 'icon' => 'fas fa-female'],
                    ['name' => "Kids' Fashion", 'icon' => 'fas fa-child'],
                    ['name' => 'Footwear', 'icon' => 'fas fa-shoe-prints'],
                    ['name' => 'Bags & Luggage', 'icon' => 'fas fa-shopping-bag'],
                    ['name' => 'Watches', 'icon' => 'fas fa-clock'],
                    ['name' => 'Jewelry', 'icon' => 'fas fa-gem'],
                ],
            ],
            [
                'name' => 'Home & Living',
                'icon' => 'fas fa-home',
                'featured' => true,
                'children' => [
                    ['name' => 'Furniture', 'icon' => 'fas fa-couch'],
                    ['name' => 'Home Decor', 'icon' => 'fas fa-paint-roller'],
                    ['name' => 'Kitchen & Dining', 'icon' => 'fas fa-utensils'],
                    ['name' => 'Bedding', 'icon' => 'fas fa-bed'],
                    ['name' => 'Lighting', 'icon' => 'fas fa-lightbulb'],
                ],
            ],
            [
                'name' => 'Beauty & Health',
                'icon' => 'fas fa-spa',
                'children' => [
                    ['name' => 'Skincare', 'icon' => 'fas fa-pump-soap'],
                    ['name' => 'Makeup', 'icon' => 'fas fa-palette'],
                    ['name' => 'Hair Care', 'icon' => 'fas fa-cut'],
                    ['name' => 'Fragrances', 'icon' => 'fas fa-spray-can'],
                    ['name' => 'Health & Wellness', 'icon' => 'fas fa-heartbeat'],
                ],
            ],
            [
                'name' => 'Sports & Outdoors',
                'icon' => 'fas fa-futbol',
                'children' => [
                    ['name' => 'Sports Equipment', 'icon' => 'fas fa-basketball-ball'],
                    ['name' => 'Fitness', 'icon' => 'fas fa-dumbbell'],
                    ['name' => 'Outdoor Recreation', 'icon' => 'fas fa-campground'],
                    ['name' => 'Sportswear', 'icon' => 'fas fa-running'],
                ],
            ],
            [
                'name' => 'Books & Stationery',
                'icon' => 'fas fa-book',
                'children' => [
                    ['name' => 'Books', 'icon' => 'fas fa-book-open'],
                    ['name' => 'Office Supplies', 'icon' => 'fas fa-paperclip'],
                    ['name' => 'Art & Craft', 'icon' => 'fas fa-palette'],
                ],
            ],
            [
                'name' => 'Groceries',
                'icon' => 'fas fa-shopping-basket',
                'children' => [
                    ['name' => 'Food & Beverages', 'icon' => 'fas fa-utensils'],
                    ['name' => 'Snacks', 'icon' => 'fas fa-cookie'],
                    ['name' => 'Organic', 'icon' => 'fas fa-leaf'],
                ],
            ],
        ];

        $order = 0;
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $slug = Str::slug($categoryData['name']);
            $imagePath = match ($slug) {
                'electronics' => 'images/categories/electronics.png',
                'fashion' => 'images/categories/fashion.png',
                'home-living' => 'images/categories/home-living.png',
                'beauty-health' => 'images/categories/beauty-health.png',
                'sports-outdoors' => 'images/categories/sports-outdoors.png',
                'books-stationery' => 'images/categories/books-stationery.png',
                'groceries' => 'images/categories/groceries.png',
                default => null,
            };

            $category = Category::updateOrCreate([
                'slug' => $slug,
            ], [
                ...$categoryData,
                'image' => $imagePath,
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]);

            $childOrder = 0;
            foreach ($children as $child) {
                Category::updateOrCreate([
                    'slug' => Str::slug($child['name']),
                ], [
                    ...$child,
                    'parent_id' => $category->id,
                    'order' => $childOrder++,
                    'is_active' => true,
                ]);
            }
        }
    }
}
