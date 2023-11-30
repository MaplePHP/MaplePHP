<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */

return [
    'routers' => [
        'load' => ["web", "cli"],
        "cache" => true,
        'cacheFile' => [
            //'driver' => 'file',
            'path' => 'storage/caches/',
            'file' => 'router.cache',
            'prefix' => 'maple_'
        ]
    ]
];
