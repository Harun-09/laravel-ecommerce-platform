<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        // Platform-wide coupons
        $coupons = [
            [
                'vendor_id' => null,
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => '10% off on your first order',
                'type' => 'percentage',
                'value' => 10,
                'minimum_order_amount' => 500,
                'maximum_discount' => 500,
                'usage_limit' => null,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
            ],
            [
                'vendor_id' => null,
                'code' => 'FLAT100',
                'name' => 'Flat ৳100 Off',
                'description' => '৳100 off on orders above ৳1000',
                'type' => 'fixed',
                'value' => 100,
                'minimum_order_amount' => 1000,
                'maximum_discount' => null,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 3,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
            ],
            [
                'vendor_id' => null,
                'code' => 'FREESHIP',
                'name' => 'Free Shipping',
                'description' => 'Free shipping on all orders',
                'type' => 'free_shipping',
                'value' => 0,
                'minimum_order_amount' => 2000,
                'maximum_discount' => null,
                'usage_limit' => 500,
                'usage_limit_per_user' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(1),
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate([
                'code' => $coupon['code'],
            ], [
                ...$coupon,
                'is_active' => true,
            ]);
        }

        // Vendor-specific coupons
        $fashionVendor = Vendor::where('shop_name', 'Fashion Hub BD')->first();
        if ($fashionVendor) {
            Coupon::updateOrCreate([
                'code' => 'FASHION20',
            ], [
                'vendor_id' => $fashionVendor->id,
                'code' => 'FASHION20',
                'name' => '20% Off Fashion',
                'description' => 'Extra 20% off on Fashion Hub products',
                'type' => 'percentage',
                'value' => 20,
                'minimum_order_amount' => 1000,
                'maximum_discount' => 1000,
                'usage_limit' => 200,
                'usage_limit_per_user' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
            ]);
        }
    }
}
