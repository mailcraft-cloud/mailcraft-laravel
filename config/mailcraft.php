<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MailCraft API Key
    |--------------------------------------------------------------------------
    |
    | Your MailCraft API key (starts with mc_).
    |
    */
    'api_key' => env('MAILCRAFT_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The MailCraft API base URL. Change this if you're self-hosting.
    |
    */
    'base_url' => env('MAILCRAFT_BASE_URL', 'https://api.mailcraft.cloud'),
];
