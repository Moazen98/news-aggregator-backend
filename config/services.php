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

    'news_api' => [
        'key' => env('NEWS_API_KEY','049c379748734914a596f7868e25581e'),
    ],
    'guardian_api' => [
        'key' => env('GUARDIAN_API_KEY','bb62af3f-cec0-428f-80e4-73c32fd29186'),
    ],
    'new_york_api' => [
        'key' => env('NEW_YORK_API_KEY','Gm0EgEDftJ6VD31tZaOVpFaYMqjAhXkm'),
    ],
    'news_api_ai' => [
        'key' => env('NEWS_API_AI_KEY','577795b5-203a-4b90-83ec-d10863a2b964'),
    ],


];
