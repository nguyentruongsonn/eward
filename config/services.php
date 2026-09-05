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

    'vietqr' => [
        'bank_id' => env('VIETQR_BANK_ID', 'MB'),
        'account_no' => env('VIETQR_ACCOUNT_NO', '914040399999'),
    ],

    'casso' => [
        'api_key' => env('CASSO_API_KEY'),
        'webhook_secret' => env('CASSO_WEBHOOK_SECRET'),
        'api_url' => env('CASSO_API_URL', 'https://oauth.casso.vn/v2/transactions'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'api_url' => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'timeout' => (int) env('GROQ_TIMEOUT', 20),
    ],

    'imap' => [
        'host' => env('IMAP_HOST', 'imap.gmail.com'),
        'port' => (int) env('IMAP_PORT', 993),
        'folder' => env('IMAP_FOLDER', 'INBOX'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'webhook_secret' => env('MAIL_WEBHOOK_SECRET'),
    ],

];
