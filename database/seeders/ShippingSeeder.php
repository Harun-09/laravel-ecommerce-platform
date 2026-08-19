<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        // Inside Dhaka
        $dhaka = ShippingZone::updateOrCreate([
            'code' => 'inside_dhaka',
        ], [
            'name' => 'Inside Dhaka',
            'code' => 'inside_dhaka',
            'regions' => ['Dhaka', 'Mirpur', 'Uttara', 'Gulshan', 'Banani', 'Dhanmondi', 'Mohammadpur', 'Motijheel'],
            'is_active' => true,
            'order' => 0,
        ]);

        ShippingMethod::updateOrCreate([
            'shipping_zone_id' => $dhaka->id,
            'name' => 'Standard Delivery',
        ], [
            'shipping_zone_id' => $dhaka->id,
            'name' => 'Standard Delivery',
            'description' => 'Delivery within 2-3 business days',
            'type' => 'flat',
            'cost' => 60,
            'cod_fee' => 20,
            'estimated_days' => '2-3 days',
            'is_cod_available' => true,
            'is_active' => true,
            'order' => 0,
        ]);

        ShippingMethod::updateOrCreate([
            'shipping_zone_id' => $dhaka->id,
            'name' => 'Express Delivery',
        ], [
            'shipping_zone_id' => $dhaka->id,
            'name' => 'Express Delivery',
            'description' => 'Same day or next day delivery',
            'type' => 'flat',
            'cost' => 100,
            'cod_fee' => 30,
            'estimated_days' => '1 day',
            'is_cod_available' => true,
            'is_active' => true,
            'order' => 1,
        ]);

        ShippingMethod::updateOrCreate([
            'shipping_zone_id' => $dhaka->id,
            'name' => 'Free Shipping',
        ], [
            'shipping_zone_id' => $dhaka->id,
            'name' => 'Free Shipping',
            'description' => 'Free delivery for orders above BDT 2000',
            'type' => 'free',
            'cost' => 60,
            'cod_fee' => 20,
            'minimum_order_amount' => 2000,
            'estimated_days' => '2-3 days',
            'is_cod_available' => true,
            'is_active' => true,
            'order' => 2,
        ]);

        // Outside Dhaka
        $outsideDhaka = ShippingZone::updateOrCreate([
            'code' => 'outside_dhaka',
        ], [
            'name' => 'Outside Dhaka',
            'code' => 'outside_dhaka',
            'regions' => ['Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barishal', 'Rangpur', 'Mymensingh'],
            'is_active' => true,
            'order' => 1,
        ]);

        ShippingMethod::updateOrCreate([
            'shipping_zone_id' => $outsideDhaka->id,
            'name' => 'Standard Delivery',
        ], [
            'shipping_zone_id' => $outsideDhaka->id,
            'name' => 'Standard Delivery',
            'description' => 'Delivery within 3-5 business days',
            'type' => 'flat',
            'cost' => 120,
            'cod_fee' => 40,
            'estimated_days' => '3-5 days',
            'is_cod_available' => true,
            'is_active' => true,
            'order' => 0,
        ]);

        ShippingMethod::updateOrCreate([
            'shipping_zone_id' => $outsideDhaka->id,
            'name' => 'Express Delivery',
        ], [
            'shipping_zone_id' => $outsideDhaka->id,
            'name' => 'Express Delivery',
            'description' => 'Delivery within 2-3 business days',
            'type' => 'flat',
            'cost' => 180,
            'cod_fee' => 50,
            'estimated_days' => '2-3 days',
            'is_cod_available' => true,
            'is_active' => true,
            'order' => 1,
        ]);

        ShippingMethod::updateOrCreate([
            'shipping_zone_id' => $outsideDhaka->id,
            'name' => 'Free Shipping',
        ], [
            'shipping_zone_id' => $outsideDhaka->id,
            'name' => 'Free Shipping',
            'description' => 'Free delivery for orders above BDT 5000',
            'type' => 'free',
            'cost' => 120,
            'cod_fee' => 40,
            'minimum_order_amount' => 5000,
            'estimated_days' => '3-5 days',
            'is_cod_available' => true,
            'is_active' => true,
            'order' => 2,
        ]);
    }
}

