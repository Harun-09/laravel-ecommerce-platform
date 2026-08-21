<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['group' => 'general', 'key' => 'site_name', 'value' => 'NovaMart', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Your Trusted Online Marketplace', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_email', 'value' => 'info@novamart.com', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_phone', 'value' => '+880 1700-000000', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_address', 'value' => 'Gulshan, Dhaka, Bangladesh', 'type' => 'textarea'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'BDT', 'type' => 'text'],
            ['group' => 'general', 'key' => 'currency_symbol', 'value' => '৳', 'type' => 'text'],
            ['group' => 'general', 'key' => 'currency_position', 'value' => 'before', 'type' => 'text'],

            // Commission Settings
            ['group' => 'commission', 'key' => 'global_commission_rate', 'value' => '10', 'type' => 'number'],
            ['group' => 'commission', 'key' => 'commission_type', 'value' => 'percentage', 'type' => 'text'],

            // Payout Settings
            ['group' => 'payout', 'key' => 'min_payout_amount', 'value' => '500', 'type' => 'number'],
            ['group' => 'payout', 'key' => 'payout_schedule', 'value' => 'weekly', 'type' => 'text'],

            // Order Settings
            ['group' => 'order', 'key' => 'order_prefix', 'value' => 'ORD', 'type' => 'text'],
            ['group' => 'order', 'key' => 'allow_guest_checkout', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'order', 'key' => 'order_cancellation_window', 'value' => '24', 'type' => 'number'],

            // Product Settings
            ['group' => 'product', 'key' => 'products_per_page', 'value' => '20', 'type' => 'number'],
            ['group' => 'product', 'key' => 'require_product_approval', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'product', 'key' => 'low_stock_threshold', 'value' => '5', 'type' => 'number'],

            // Vendor Settings
            ['group' => 'vendor', 'key' => 'require_vendor_approval', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'vendor', 'key' => 'vendor_registration_enabled', 'value' => '1', 'type' => 'boolean'],

            // Tax Settings
            ['group' => 'tax', 'key' => 'tax_enabled', 'value' => '0', 'type' => 'boolean'],
            ['group' => 'tax', 'key' => 'tax_rate', 'value' => '0', 'type' => 'number'],

            // Social Links
            ['group' => 'social', 'key' => 'facebook_url', 'value' => 'https://facebook.com/novamart', 'type' => 'text'],
            ['group' => 'social', 'key' => 'instagram_url', 'value' => 'https://instagram.com/novamart', 'type' => 'text'],
            ['group' => 'social', 'key' => 'youtube_url', 'value' => 'https://youtube.com/@novamart', 'type' => 'text'],
            ['group' => 'social', 'key' => 'twitter_url', 'value' => 'https://x.com/novamart', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
