<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Winter Collection 2026',
                'subtitle' => 'Up to 50% Off on Winter Wear',
                'image' => 'images/banners/winter-sale.jpg',
                'link' => '/category/fashion',
                'button_text' => 'Shop Now',
                'position' => 'hero',
                'order' => 0,
            ],
            [
                'title' => 'New Arrivals',
                'subtitle' => 'Latest Electronics at Best Prices',
                'image' => 'images/banners/electronics.jpg',
                'link' => '/category/electronics',
                'button_text' => 'Explore',
                'position' => 'hero',
                'order' => 1,
            ],
            [
                'title' => 'Free Shipping',
                'subtitle' => 'On orders above ৳2000',
                'image' => 'images/banners/free-shipping.jpg',
                'link' => null,
                'button_text' => null,
                'position' => 'hero',
                'order' => 2,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate([
                'position' => $banner['position'],
                'order' => $banner['order'],
            ], [
                ...$banner,
                'is_active' => true,
            ]);
        }
    }
}
