<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use App\Models\Banner;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            AttributeSeeder::class,
            VendorSeeder::class,
            ProductSeeder::class,
            FilterCoverageSeeder::class,
            ProductImageGallerySeeder::class,
            ShippingSeeder::class,
            BannerSeeder::class,
            PageSeeder::class,
            EmailTemplateSeeder::class,
            SettingSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
