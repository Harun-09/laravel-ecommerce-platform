<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        // Size Attribute
        $size = Attribute::updateOrCreate([
            'slug' => Str::slug('Size'),
        ], [
            'name' => 'Size',
            'type' => 'button',
            'is_filterable' => true,
            'order' => 0,
        ]);

        foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'] as $i => $value) {
            AttributeValue::updateOrCreate([
                'attribute_id' => $size->id,
                'slug' => Str::slug($value),
            ], [
                'attribute_id' => $size->id,
                'value' => $value,
                'order' => $i,
            ]);
        }

        // Color Attribute
        $color = Attribute::updateOrCreate([
            'slug' => Str::slug('Color'),
        ], [
            'name' => 'Color',
            'type' => 'color',
            'is_filterable' => true,
            'order' => 1,
        ]);

        $colors = [
            'Black' => '#000000',
            'White' => '#FFFFFF',
            'Red' => '#EF4444',
            'Blue' => '#3B82F6',
            'Green' => '#22C55E',
            'Yellow' => '#EAB308',
            'Purple' => '#A855F7',
            'Pink' => '#EC4899',
            'Orange' => '#F97316',
            'Gray' => '#6B7280',
            'Navy' => '#1E3A5F',
            'Brown' => '#92400E',
        ];

        $i = 0;
        foreach ($colors as $name => $code) {
            AttributeValue::updateOrCreate([
                'attribute_id' => $color->id,
                'slug' => Str::slug($name),
            ], [
                'attribute_id' => $color->id,
                'value' => $name,
                'color_code' => $code,
                'order' => $i++,
            ]);
        }

        // Storage Attribute (for electronics)
        $storage = Attribute::updateOrCreate([
            'slug' => Str::slug('Storage'),
        ], [
            'name' => 'Storage',
            'type' => 'select',
            'is_filterable' => true,
            'order' => 2,
        ]);

        foreach (['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'] as $i => $value) {
            AttributeValue::updateOrCreate([
                'attribute_id' => $storage->id,
                'slug' => Str::slug($value),
            ], [
                'attribute_id' => $storage->id,
                'value' => $value,
                'order' => $i,
            ]);
        }

        // RAM Attribute
        $ram = Attribute::updateOrCreate([
            'slug' => Str::slug('RAM'),
        ], [
            'name' => 'RAM',
            'type' => 'select',
            'is_filterable' => true,
            'order' => 3,
        ]);

        foreach (['4GB', '6GB', '8GB', '12GB', '16GB', '32GB'] as $i => $value) {
            AttributeValue::updateOrCreate([
                'attribute_id' => $ram->id,
                'slug' => Str::slug($value),
            ], [
                'attribute_id' => $ram->id,
                'value' => $value,
                'order' => $i,
            ]);
        }
    }
}
