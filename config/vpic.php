<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NHTSA vPIC API (Vehicle Product Information Catalog)
    |--------------------------------------------------------------------------
    |
    | Official read-only API — no API key. Used by listing-options:sync-vpic.
    | https://vpic.nhtsa.dot.gov/api/
    |
    */

    'base_url' => rtrim((string) env('VPIC_BASE_URL', 'https://vpic.nhtsa.dot.gov/api'), '/'),

    'delay_ms' => (int) env('VPIC_SYNC_DELAY_MS', 300),

    'timeout' => (int) env('VPIC_TIMEOUT', 15),

    'retries' => (int) env('VPIC_RETRIES', 3),

    'retry_sleep_ms' => (int) env('VPIC_RETRY_SLEEP_MS', 500),

    'user_agent' => (string) env('VPIC_USER_AGENT', 'MyAutoTorque/vPIC-Sync (+https://vpic.nhtsa.dot.gov/api/)'),
];
