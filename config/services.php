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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'sms' => [
        'url'       => env('SMS_API_URL', 'https://sms.vutia.co.ke/api/services/sendotp'),
        'apikey'    => env('SMS_API_KEY', '5dea4ae7356828c608dc74670393c3bc'),
        'partnerID' => env('SMS_PARTNER_ID', '6438'),
        'shortcode' => env('SMS_SHORTCODE', 'TIKITI'),
        'default_country' => env('SMS_DEFAULT_COUNTRY', '254'), // for normalization
    ],

    

];
