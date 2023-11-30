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
        'lang' => 'sv',
        'public_dir' => 'public/',
        'version' => '4.0.0',
        'bundle' => $this->getenv("NONCE"),
        'maintainer' => 'Daniel Ronkainen <daniel@creativearmy.se>'
    ],
    'providers' => [
        'services' => [
            'logger' => '\Services\ServiceLogger',
            'lang' => '\Services\ServiceLang',
            'responder' => '\Services\ServiceResponder',
            'cookies' => '\Services\ServiceCookie'
        ]
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
            "style-src" => "'self'",
            "object-src" => "'self'",
            "img-src" => "'self'",
            "frame-ancestors" => "'self'",
            "form-action" => "'self'",
            "base-uri" => "'self'"
        ]
    ],
    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 465,
        'username' => '',
        'password' => '',
        'encryption' => 'ssl', // ssl/tls
        'fromEmail' => '',
        'fromName' => ''
    ]
];
