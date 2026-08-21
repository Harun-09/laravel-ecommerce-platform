<?php

namespace Database\Seeders;

use App\Models\User;
use App\Domains\ECommerce\Models\Vendor;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Brand;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\ProductImage;
use App\Domains\ECommerce\Models\Attribute;
use App\Domains\ECommerce\Models\AttributeValue;
use App\Domains\ECommerce\Models\ShippingZone;
use App\Domains\ECommerce\Models\ShippingMethod;
use App\Domains\ECommerce\Models\Banner;
use App\Domains\ECommerce\Models\Page;
use App\Domains\ECommerce\Models\Setting;
use App\Domains\ECommerce\Models\Coupon;
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
