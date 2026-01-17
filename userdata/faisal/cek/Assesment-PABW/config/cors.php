<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Jalur yang diizinkan (api/* sudah benar karena mencakup api/v1/*)
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*', '*'],

    'allowed_methods' => ['*'],

    // UNTUK PENGEMBANGAN: Mengizinkan semua origin agar Flutter Web lancar
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];