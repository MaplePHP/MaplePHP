<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */

return [
    "mysql" => [
        'host' => [
            "prompt" => "host",
            "default" => "localhost",
            "validate" => [
                "required" => []
            ]
        ],
        'database' => [
            "prompt" => "Database name",
            "validate" => [
                "required" => []
            ]
        ],
        'username' => 'root',
        'password' => [
            "type" => "masked",
            "prompt" => "Password"
        ],
        'prefix' => 'maple_',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ]
];
