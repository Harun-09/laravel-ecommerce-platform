<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::updateOrCreate([
            'email' => 'superadmin@novamart.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $superAdmin->syncRoles(['super-admin']);

        // Admin
        $admin = User::updateOrCreate([
            'email' => 'admin@novamart.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $admin->syncRoles(['admin']);

        // Vendor Users (will be linked to vendors)
        $vendorUser1 = User::updateOrCreate([
            'email' => 'vendor1@novamart.com',
        ], [
            'name' => 'Fashion Hub BD',
            'phone' => '01711111111',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $vendorUser1->syncRoles(['vendor']);

        $vendorUser2 = User::updateOrCreate([
            'email' => 'vendor2@novamart.com',
        ], [
            'name' => 'Tech Galaxy',
            'phone' => '01722222222',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $vendorUser2->syncRoles(['vendor']);

        $vendorUser3 = User::updateOrCreate([
            'email' => 'vendor3@novamart.com',
        ], [
            'name' => 'Home Essentials',
            'phone' => '01733333333',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $vendorUser3->syncRoles(['vendor']);

        // Customer Users
        for ($i = 1; $i <= 10; $i++) {
            $customer = User::updateOrCreate([
                'email' => "customer{$i}@novamart.com",
            ], [
                'name' => "Customer {$i}",
                'email' => "customer{$i}@novamart.com",
                'phone' => '017' . str_pad((string) $i, 8, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]);
            $customer->syncRoles(['customer']);
        }
    }
}
