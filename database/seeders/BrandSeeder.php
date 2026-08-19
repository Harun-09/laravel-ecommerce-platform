<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Samsung', 'featured' => true],
            ['name' => 'Apple', 'featured' => true],
            ['name' => 'Xiaomi', 'featured' => true],
            ['name' => 'OnePlus', 'featured' => false],
            ['name' => 'Sony', 'featured' => true],
            ['name' => 'LG', 'featured' => false],
            ['name' => 'HP', 'featured' => true],
            ['name' => 'Dell', 'featured' => true],
            ['name' => 'Lenovo', 'featured' => false],
            ['name' => 'Asus', 'featured' => false],
            ['name' => 'Nike', 'featured' => true],
            ['name' => 'Adidas', 'featured' => true],
            ['name' => 'Puma', 'featured' => false],
            ['name' => 'Yellow', 'featured' => true],
            ['name' => 'Apex', 'featured' => false],
            ['name' => 'Bata', 'featured' => true],
            ['name' => 'Aarong', 'featured' => true],
            ['name' => 'Sailor', 'featured' => false],
            ['name' => 'Richman', 'featured' => false],
            ['name' => 'Raymond', 'featured' => false],
        ];

        $order = 0;
        foreach ($brands as $brand) {
            $slug = Str::slug($brand['name']);
            Brand::updateOrCreate([
                'slug' => $slug,
            ], [
                ...$brand,
                'logo' => 'images/brands/' . $slug . '.png',
                'order' => $order++,
                'is_active' => true,
            ]);
        }
    }
}
