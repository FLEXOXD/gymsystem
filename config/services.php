<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'webpush' => [
        'enabled' => (bool) env('WEBPUSH_ENABLED', false),
        'vapid' => [
            'subject' => env('WEBPUSH_VAPID_SUBJECT', 'mailto:soporte@gymsystem.app'),
            'public_key' => env('WEBPUSH_VAPID_PUBLIC_KEY'),
            'private_key' => env('WEBPUSH_VAPID_PRIVATE_KEY'),
        ],
        'openssl' => [
            'conf' => env('WEBPUSH_OPENSSL_CONF', env('OPENSSL_CONF')),
            'rand_file' => env('WEBPUSH_RAND_FILE', env('RANDFILE')),
        ],
        'proxy' => env('WEBPUSH_PROXY'),
        'campaign_dispatch' => env('WEBPUSH_CAMPAIGN_DISPATCH', 'auto'),
    ],

];
