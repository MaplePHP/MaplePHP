<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */
return [
    'providers' => [
        'services' => [
            'logger' => '\MaplePHP\Foundation\Log\StreamLogger',
            'lang' => '\MaplePHP\Foundation\Http\Lang',
            'responder' => '\MaplePHP\Foundation\Http\Responder',
            'cookies' => '\MaplePHP\Foundation\Http\Cookie'
        ]
    ]
];