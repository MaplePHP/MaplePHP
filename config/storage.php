<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */

return [
    'log' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => '/path/to/cache',
            ],
        ],
        'prefix' => 'maple_',
        'expiration' => 60,
    ],
    'cache' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => '/path/to/cache',
            ],
        ],
        'prefix' => 'maple_',
        'expiration' => 60,
    ]
];
