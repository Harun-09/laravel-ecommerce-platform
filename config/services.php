<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'mode' => env('STRIPE_MODE', 'sandbox'),
        'sandbox_public_key' => env('STRIPE_SANDBOX_PUBLIC_KEY'),
        'sandbox_secret_key' => env('STRIPE_SANDBOX_SECRET_KEY'),
        'live_public_key' => env('STRIPE_LIVE_PUBLIC_KEY'),
        'live_secret_key' => env('STRIPE_LIVE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'bdt'),
        'display_name' => env('STRIPE_BRAND_DISPLAY_NAME', 'NovaMart'),
        'button_color' => env('STRIPE_BRAND_BUTTON_COLOR', '#1d4ed8'),
        'background_color' => env('STRIPE_BRAND_BACKGROUND_COLOR', '#f8fafc'),
        'border_style' => env('STRIPE_BRAND_BORDER_STYLE', 'rounded'),
        'font_family' => env('STRIPE_BRAND_FONT_FAMILY', 'inter'),
        'logo_url' => env('STRIPE_BRAND_LOGO_URL'),
        'icon_url' => env('STRIPE_BRAND_ICON_URL'),
    ],

    // --- SSLCOMMERZ ---
    'sslcommerz' => [
        'sandbox' => (bool) env('SSLCOMMERZ_SANDBOX', true),
        'store_id' => env('SSLCOMMERZ_STORE_ID', ''),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', ''),
        'multi_card_name' => env('SSLCOMMERZ_MULTI_CARD_NAME', ''),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', env('APP_URL') . '/auth/facebook/callback'),
    ],

    'sms' => [
        'enabled' => (bool) env('SMS_ENABLED', false),
        'provider' => env('SMS_PROVIDER', 'log'),
        'api_url' => env('SMS_API_URL'),
        'api_token' => env('SMS_API_TOKEN'),
        'from' => env('SMS_FROM', 'NovaMart'),
        'timeout' => (int) env('SMS_TIMEOUT', 10),
    ],

];
