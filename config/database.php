<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */

return [
    "mysql" => [
        'host' => [
            "type" => "text",
            "message" => "Set hostname",
            "default" => "localhost",
            "validate" => [
                "length" => [1, 60]
            ]
        ],
        'database' => [
            "type" => "text",
            "message" => "Database name",
            "default" => "maplephp",
            "validate" => [
                "length" => [1, 60]
            ]
        ],
        'username' => [
            "type" => "text",
            "message" => "Username",
            "default" => "root",
            "validate" => [
                "length" => [1, 60]
            ]
        ],
        'password' => [
            "type" => "password",
            "message" => "Password",
            "validate" => [
                "length" => [0, 60]
            ]
        ],
        'port' => [
            "type" => "text",
            "message" => "Port",
            "default" => "3306",
            "validate" => [
                "length" => [1, 4],
                "int" => []
            ]
        ],
        'prefix' => [
            "type" => "text",
            "message" => "Table prefix",
            "default" => "maple_",
            "validate" => [
                "length" => [1, 30]
            ]
        ],
        'charset' => [
            "type" => "text",
            "message" => "Set default charset",
            "default" => "utf8mb4",
            "validate" => [
                "length" => [1, 60]
            ]
        ],
        'collation' => [
            "type" => "text",
            "message" => "Set default collation",
            "default" => "utf8mb4_unicode_ci",
            "validate" => [
                "length" => [1, 60]
            ]
        ]
    ]
];