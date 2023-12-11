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

/*
Example:

// Add to service provider
'logger' => '\MaplePHP\Foundation\Log\StreamLogger'

OR 

// Add to service provider and event handler
// Event handler will trigger every time "emergency, alert or critical" is triggered
// When they are triggerd the event "PHPMailerTest" will be triggered
'logger' => [
    "handlers" => [
        '\MaplePHP\Foundation\Log\StreamLogger' => ["emergency", "alert", "critical"],
    ],
    "events" => [
        '\MaplePHP\Foundation\Mail\PHPMailerTest'
    ]
]
 */