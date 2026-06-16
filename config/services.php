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

    'resend'   => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses'      => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack'    => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Arkesel SMS credentials
    'arkesel'  => [
        'api_key'   => env('ARKESEL_API_KEY'),
        'sender_id' => env('ARKESEL_SENDER_ID'),
    ],

    // Genova credentials
    'genova'   => [
        'base_url' => env('GENOVA_BASE_URL'),
        'username' => env('GENOVA_USERNAME'),
        'password' => env('GENOVA_PASSWORD'),
    ],

    // Glims credentials
    'glims'    => [
        'url'    => env('GLIMS_API_URL'),
        'key'    => env('GLIMS_API_KEY'),
        'secret' => env('GLIMS_API_SECRET'),
    ],

];
