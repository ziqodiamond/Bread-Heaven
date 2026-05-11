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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'biteship' => [

        'base_url' => env('BITESHIP_BASE_URL'),

        'api_key' => env('BITESHIP_API_KEY'),
    ],

    'midtrans' => [

        'server_key' => env('MIDTRANS_SERVER_KEY'),

        'client_key' => env('MIDTRANS_CLIENT_KEY'),

        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

        'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

        'is_3ds' => env('MIDTRANS_IS_3DS', true),

        'enabled_payments' => [
            'credit_card',
            'bank_transfer',
            'echannel',
            'cimb_clicks',
            'bca_clicks',
            'bri_clicks',
            'danamon_online',
            'mandiri_clickpay',
            'bdo_uatpay',
            'maybank_go',
            'uob_click',
            'gopay',
            'shopeepay',
            'convstore',
            'indomart',
            'alfamart',
            'akulaku',
        ],
    ],

];
