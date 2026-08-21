<?php

namespace Database\Seeders;

use App\Models\User;
use App\Domains\ECommerce\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'user_email' => 'vendor1@novamart.com',
                'shop_name' => 'Fashion Hub BD',
                'description' => 'Your one-stop destination for trendy fashion. We offer the latest styles in men\'s, women\'s, and kids\' clothing at affordable prices.',
                'phone' => '01711111111',
                'email' => 'contact@fashionhubbd.com',
                'address' => 'Shop 15, Floor 2, New Market',
                'city' => 'Dhaka',
                'status' => 'approved',
                'commission_rate' => 12,
                'featured' => true,
            ],
            [
                'user_email' => 'vendor2@novamart.com',
                'shop_name' => 'Tech Galaxy',
                'description' => 'Leading electronics retailer offering genuine products. From smartphones to laptops, we have everything you need with warranty.',
                'phone' => '01722222222',
                'email' => 'sales@techgalaxy.com.bd',
                'address' => 'Level 5, Multiplan Center, Elephant Road',
                'city' => 'Dhaka',
                'status' => 'approved',
                'commission_rate' => 8,
                'featured' => true,
            ],
            [
                'user_email' => 'vendor3@novamart.com',
                'shop_name' => 'Home Essentials',
                'description' => 'Transform your living space with our curated collection of home decor, furniture, and kitchen essentials.',
                'phone' => '01733333333',
                'email' => 'info@homeessentials.com.bd',
                'address' => 'House 45, Road 11, Banani',
                'city' => 'Dhaka',
                'status' => 'approved',
                'commission_rate' => 10,
                'featured' => false,
            ],
        ];

        foreach ($vendors as $vendorData) {
            $user = User::where('email', $vendorData['user_email'])->first();
            unset($vendorData['user_email']);

            if (!$user) {
                continue;
            }

            Vendor::updateOrCreate([
                'user_id' => $user->id,
            ], [
                ...$vendorData,
                'country' => 'Bangladesh',
                'approved_at' => now(),
            ]);
        }
    }
}
