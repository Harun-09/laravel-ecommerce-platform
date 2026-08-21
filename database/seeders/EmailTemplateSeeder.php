<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Order Placed',
                'slug' => 'order-placed',
                'subject' => 'Order Placed: #{{order_number}}',
                'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>#{{order_number}}</strong> has been placed successfully.</p><p><strong>Order Total:</strong> {{order_total}}</p><p>You can check your order details here: <a href="{{order_url}}">{{order_url}}</a></p><p>Thank you for shopping with NovaMart.</p>',
                'variables' => ['customer_name', 'order_number', 'order_total', 'order_url'],
                'is_active' => true,
            ],
            [
                'name' => 'Order Shipped',
                'slug' => 'order-shipped',
                'subject' => 'Order Shipped: #{{order_number}}',
                'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>#{{order_number}}</strong> has been shipped.</p><p><strong>Carrier:</strong> {{shipping_carrier}}<br><strong>Tracking Number:</strong> {{tracking_number}}</p><p>Track your order here: <a href="{{order_url}}">{{order_url}}</a></p>',
                'variables' => ['customer_name', 'order_number', 'shipping_carrier', 'tracking_number', 'order_url'],
                'is_active' => true,
            ],
            [
                'name' => 'Order Delivered',
                'slug' => 'order-delivered',
                'subject' => 'Order Delivered: #{{order_number}}',
                'body' => '<p>Hi {{customer_name}},</p><p>Your order <strong>#{{order_number}}</strong> has been delivered successfully.</p><p>We hope you enjoy your purchase.</p><p>Need help? Reply to this email.</p>',
                'variables' => ['customer_name', 'order_number'],
                'is_active' => true,
            ],
            [
                'name' => 'Order Refund',
                'slug' => 'order-refund',
                'subject' => 'Refund Update: #{{order_number}}',
                'body' => '<p>Hi {{customer_name}},</p><p>A refund has been processed for order <strong>#{{order_number}}</strong>.</p><p><strong>Refund Amount:</strong> {{refund_amount}}</p><p>If this is unexpected, contact support immediately.</p>',
                'variables' => ['customer_name', 'order_number', 'refund_amount'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
