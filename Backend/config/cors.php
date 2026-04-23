<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173', 
        'https://sice.up.railway.app' // <--- Sin la "/" al final
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => false,
];