<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h2>Welcome to NovaMart</h2>
<p>NovaMart is Bangladesh\'s leading multi-vendor e-commerce platform, connecting millions of customers with trusted sellers across the country.</p>
<h3>Our Mission</h3>
<p>To provide a seamless and secure online shopping experience while empowering local businesses to reach customers nationwide.</p>
<h3>Why Choose NovaMart?</h3>
<ul>
<li>Wide range of products from verified sellers</li>
<li>Secure payment options including COD</li>
<li>Fast and reliable delivery</li>
<li>24/7 Customer Support</li>
<li>Easy returns and refunds</li>
</ul>',
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => '<h2>Terms and Conditions</h2>
<p>By accessing and using NovaMart, you accept and agree to be bound by the terms and conditions outlined herein.</p>
<h3>1. Use of Website</h3>
<p>You may use our website for lawful purposes only. You must not use our website in any way that causes damage to the website or impairs the availability of the website.</p>
<h3>2. Account Registration</h3>
<p>You must provide accurate and complete information when creating an account. You are responsible for maintaining the confidentiality of your account credentials.</p>
<h3>3. Orders and Payments</h3>
<p>All orders are subject to availability and confirmation of the order price. We reserve the right to refuse or cancel any order for any reason.</p>
<h3>4. Shipping and Delivery</h3>
<p>Delivery times are estimates only. We are not responsible for delays caused by circumstances beyond our control.</p>',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2>
<p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal information.</p>
<h3>Information We Collect</h3>
<ul>
<li>Personal information (name, email, phone number)</li>
<li>Shipping and billing addresses</li>
<li>Payment information</li>
<li>Order history</li>
</ul>
<h3>How We Use Your Information</h3>
<ul>
<li>To process and fulfill your orders</li>
<li>To communicate with you about your orders</li>
<li>To improve our services</li>
<li>To send promotional communications (with your consent)</li>
</ul>
<h3>Data Security</h3>
<p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>',
            ],
            [
                'title' => 'Return & Refund Policy',
                'slug' => 'return-refund-policy',
                'content' => '<h2>Return & Refund Policy</h2>
<h3>Return Eligibility</h3>
<p>Items can be returned within 7 days of delivery if:</p>
<ul>
<li>The item is damaged or defective</li>
<li>The item received is different from what was ordered</li>
<li>The item is unused and in its original packaging</li>
</ul>
<h3>Non-Returnable Items</h3>
<ul>
<li>Perishable goods</li>
<li>Personal care items</li>
<li>Customized products</li>
<li>Digital products</li>
</ul>
<h3>Refund Process</h3>
<p>Refunds will be processed within 7-10 business days after we receive and inspect the returned item.</p>',
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => '<h2>Contact Us</h2>
<p>We\'re here to help! Reach out to us through any of the following channels:</p>
<h3>Customer Support</h3>
<p><strong>Phone:</strong> +880 1700-000000</p>
<p><strong>Email:</strong> support@novamart.com</p>
<p><strong>Hours:</strong> Saturday - Thursday, 9:00 AM - 9:00 PM</p>
<h3>Office Address</h3>
<p>NovaMart Headquarters<br>House 123, Road 45<br>Gulshan, Dhaka 1212<br>Bangladesh</p>
<h3>For Vendors</h3>
<p><strong>Email:</strong> vendors@novamart.com</p>
<p><strong>Phone:</strong> +880 1700-000001</p>',
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate([
                'slug' => $page['slug'],
            ], [
                ...$page,
                'is_active' => true,
            ]);
        }
    }
}
