<?php

return [
    'database' => [
        'dbname' => getenv('DDD_RBD_NAME'),
        'user' => getenv('DDD_RBD_USER'),
        'password' => getenv('DDD_RBD_PASSWORD'),
        'host' => getenv('DDD_RBD_HOST'),
        'driver' => getenv('DDD_RBD_DRIVER'),
        'charset' => getenv('DDD_RBD_CONNECTION_CHARSET') ?: 'utf8',
    ],
    'loggingType' => getenv('LOGGING_TYPE'),
];
