<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
    'http://localhost:5173', 
    'https://considerate-success-production-b16e.up.railway.app/' // Añade tu URL de producción aquí
],
    'allowed_headers' => ['*'],
    'supports_credentials' => false,
];
