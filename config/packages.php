<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */

return [
    'login' => [
        'package' => 'login',
        'vesion' => '1.0',
        'maintainer' => 'Daniel Ronkainen <daniel@creativearmy.se>',
        'description' => 'Login service',
        'files' => [
            'app/Http/Controllers/Users/Login.php',
            'app/Http/Controllers/Examples/PrivatePage.php',
            'app/Services/Users/LoginService.php'
        ]
    ]
];
