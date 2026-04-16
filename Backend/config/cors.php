<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // 👈 IMPORTANTE
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // puedes restringir luego
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
