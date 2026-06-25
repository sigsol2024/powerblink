<?php

return [

    'bootstrap_admin_email' => env('BOOTSTRAP_ADMIN_EMAIL', 'info@powerblinkfc.com'),

    'bootstrap_admin_password' => env('BOOTSTRAP_ADMIN_PASSWORD'),

    'bootstrap_admin_name' => env('BOOTSTRAP_ADMIN_NAME', 'PowerBlink Admin'),

    'demo_user_password' => env('DEMO_USER_PASSWORD'),

    'payment_pending_reuse_minutes' => (int) env('PAYMENT_PENDING_REUSE_MINUTES', 30),

    'site_url' => env('POWERBLINK_SITE_URL', 'https://powerblinkfc.com'),

];
