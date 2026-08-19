<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permission catalog
        $permissions = [
            // Admin
            'view dashboard',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view vendors',
            'create vendors',
            'edit vendors',
            'delete vendors',
            'approve vendors',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'approve products',
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'process orders',
            'cancel orders',
            'view payments',
            'process payments',
            'process refunds',
            'view payouts',
            'create payouts',
            'process payouts',
            'view coupons',
            'create coupons',
            'edit coupons',
            'delete coupons',
            'view reviews',
            'approve reviews',
            'delete reviews',
            'view reports',
            'export reports',
            'view settings',
            'edit settings',
            'view banners',
            'create banners',
            'edit banners',
            'delete banners',
            'view pages',
            'create pages',
            'edit pages',
            'delete pages',

            // Vendor
            'view vendor dashboard',
            'manage own products',
            'manage own orders',
            'view vendor earnings',

            // Customer
            'view customer dashboard',
            'view own orders',
            'cancel own orders',
            'manage profile',
            'manage addresses',
            'manage wishlist',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Super Admin gets every permission
        $superAdmin->syncPermissions(Permission::all());

        // Admin permissions
        $adminPermissions = [
            'view dashboard',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view vendors',
            'create vendors',
            'edit vendors',
            'delete vendors',
            'approve vendors',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'approve products',
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'process orders',
            'cancel orders',
            'view payments',
            'process payments',
            'process refunds',
            'view payouts',
            'create payouts',
            'process payouts',
            'view coupons',
            'create coupons',
            'edit coupons',
            'delete coupons',
            'view reviews',
            'approve reviews',
            'delete reviews',
            'view reports',
            'export reports',
            'view settings',
            'view banners',
            'create banners',
            'edit banners',
            'delete banners',
            'view pages',
            'create pages',
            'edit pages',
            'delete pages',
        ];
        $admin->syncPermissions($adminPermissions);

        // Vendor permissions
        $vendorPermissions = [
            'view vendor dashboard',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view orders',
            'process orders',
            'view payments',
            'view payouts',
            'manage own products',
            'manage own orders',
            'view vendor earnings',
            'view reports',
            'export reports',
        ];
        $vendor->syncPermissions($vendorPermissions);

        // Customer permissions
        $customerPermissions = [
            'create orders',
            'view customer dashboard',
            'view own orders',
            'cancel own orders',
            'manage profile',
            'manage addresses',
            'manage wishlist',
        ];
        $customer->syncPermissions($customerPermissions);
    }
}
