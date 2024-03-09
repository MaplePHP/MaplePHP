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
        'port' => [
            "prompt" => "Port",
            "default" => "3306",
            "validate" => [
                "int" => []
            ]
        ],
        'prefix' => 'maple_',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ]
];
