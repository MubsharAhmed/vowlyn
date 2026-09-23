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

    'google_analytics' => [
        'measurement_id' => env('GA4_MEASUREMENT_ID', 'G-XBKNTW9W3M'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Company profiles
    |--------------------------------------------------------------------------
    |
    | The accounts Vowlyn actually maintains. Linked from the footer and
    | declared as Organization sameAs, so both stay in step when one changes.
    |
    */

    'social' => [
        'linkedin' => env('SOCIAL_LINKEDIN_URL', 'https://www.linkedin.com/company/vowlyn'),
    ],

];
