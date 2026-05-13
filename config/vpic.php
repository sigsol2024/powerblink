<?php

/*
| Curated marketplace makes: stable NHTSA vPIC Make_ID values only (never names).
| Validate or extend via GET https://vpic.nhtsa.dot.gov/api/vehicles/GetAllMakes?format=json
|
| If VPIC_ALLOWED_MAKE_IDS is set to a non-empty comma-separated list, it replaces this file list entirely.
*/

$fileAllowedMakeIds = [
    440, 441, 442, 443, 444, 445, 448, 449, 452, 454, 456, 460, 464, 466, 467, 468, 469, 472, 473, 474, 475, 476, 477,
    478, 480, 481, 482, 483, 485, 492, 493, 496, 498, 499, 502, 504, 515, 523, 5554, 582, 583, 584, 603, 1991, 2236,
    5083, 10224, 10887, 10919, 11366, 11856, 11921, 12144, 12360, 13271,
];

$env = env('VPIC_ALLOWED_MAKE_IDS');
if (is_string($env) && trim($env) !== '') {
    $parsed = [];
    foreach (explode(',', $env) as $part) {
        $part = trim($part);
        if ($part === '' || ! ctype_digit($part)) {
            continue;
        }
        $parsed[] = (int) $part;
    }
    $allowedMakeIds = array_values(array_unique($parsed));
} else {
    $allowedMakeIds = array_values(array_unique($fileAllowedMakeIds));
}

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

    /*
    |--------------------------------------------------------------------------
    | Curated Make_ID allowlist
    |--------------------------------------------------------------------------
    |
    | Only these vPIC makes are synced (insert/update) and receive model imports.
    | Use listing-options:prune-vpic to deactivate unused vPIC rows outside this list.
    |
    */
    'allowed_make_ids' => $allowedMakeIds,
];
