<?php

/**
 * Config file
 * @psalm-suppress InvalidScope
 */

return [
    'stripe' => [
        'key' => 'my_stripe_key',
        'secret' => 'my_stripe_secret',
    ],
    'aws' => [
        'key' => 'my_aws_key',
        'secret' => 'my_aws_secret',
        'region' => 'us-west-2',
        'bucket' => 'my_aws_bucket',
    ],
    'google' => [
        'client_id' => 'my_google_client_id',
        'client_secret' => 'my_google_client_secret',
        'redirect_uri' => 'https://myapp.com/auth/google/callback',
    ]
];
