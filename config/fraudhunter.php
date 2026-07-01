<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FraudHunter API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for your FraudHunter instance.
    |
    */
    'api_url' => env('FRAUDHUNTER_API_URL', 'http://localhost:8080'),

    /*
    |--------------------------------------------------------------------------
    | FraudHunter API Key
    |--------------------------------------------------------------------------
    |
    | The API Key generated from the FraudHunter dashboard.
    |
    */
    'api_key' => env('FRAUDHUNTER_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Platform Name
    |--------------------------------------------------------------------------
    |
    | Identifying the platform sending the data (e.g., 'WL', 'IAK', 'Buylink').
    | Required — must be set via FRAUDHUNTER_PLATFORM in your .env file.
    |
    */
    'platform' => env('FRAUDHUNTER_PLATFORM', ''),

    /*
    |--------------------------------------------------------------------------
    | Automatic Event Mapping
    |--------------------------------------------------------------------------
    |
    | Map standard Laravel events to FraudHunter activity types.
    |
    */
    'event_map' => [
        'Illuminate\Auth\Events\Login' => 'LOGIN',
        'Illuminate\Auth\Events\PasswordReset' => 'RESET_PASSWORD',
        'Illuminate\Auth\Events\Logout' => 'LOGOUT',
        // 'App\Events\OrderPlaced' => 'TRANSACTION',
    ],
];
