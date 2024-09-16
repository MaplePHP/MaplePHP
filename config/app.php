<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */

return [
    'app' => [
        'name' => 'My Awesome App',
        'description' => 'Lorem ipsum dolor sit amet.',
        'env' => 'development',
        'debug' => 1,
        'charset' => 'UTF-8',
        'ssl' => 1,
        'lang' => 'en',
        'public_dir' => 'public/',
        'version' => '4.0.0',
        'bundle' => $this->getenv("NONCE"),
        'maintainer' => 'John Doe <john.doe@gmail.com>'
    ],
    'configs' => [
        'database',
        'navigation',
        'providers',
        'routers'
    ],
    'session' => [
        "time" => 360, // minutes
        "ssl" => 1 // Strict: SSL only flag
    ],
    'headers' => [
        "Content-type" => "text/html; charset=" . $this->getenv("APP_CHARSET", "UTF-8"),
        "X-Frame-Options" => "SAMEORIGIN",
        "X-XSS-Protection" => "1",
        "X-Content-Type-Options" => "nosniff",
        "Strict-Transport-Security" => "max-age=31536000; includeSubDomains",
        "Content-Security-Policy" => [
            "default-src" => "'self'",
            "script-src" => "'nonce-" . $this->getenv("NONCE") . "'",
            "script-src-elem" => "'self' 'unsafe-inline'",
            "style-src" => "'self' 'unsafe-inline'",
            "object-src" => "'self'",
            "img-src" => "'self'",
            "frame-ancestors" => "'self'",
            "form-action" => "'self'",
            "base-uri" => "'self'"
        ]
    ],
    'mail' => [
        'host' => '',
        'port' => '',
        'username' => '',
        'password' => '',
        'encryption' => 'ssl', // ssl/tls
        'fromEmail' => 'john.doe@gmail.com',
        'fromName' => 'John Doe'
    ]
];
