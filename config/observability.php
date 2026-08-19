<?php

return [
    'error_tracking' => [
        'enabled' => (bool) env('ERROR_TRACKING_ENABLED', false),
        'provider' => env('ERROR_TRACKING_PROVIDER', 'sentry'),
        'sentry_dsn' => env('SENTRY_DSN'),
        'bugsnag_api_key' => env('BUGSNAG_API_KEY'),
        'environment' => env('ERROR_TRACKING_ENVIRONMENT', env('APP_ENV', 'production')),
    ],

    'payment_failure_alert' => [
        'threshold' => (int) env('OBS_PAYMENT_FAILURE_THRESHOLD', 3),
        'window_minutes' => (int) env('OBS_PAYMENT_FAILURE_WINDOW_MINUTES', 15),
    ],

    'audit_critical_events' => [
        'payout.approved',
    ],
];
