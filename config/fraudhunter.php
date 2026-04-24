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
    | Service Name
    |--------------------------------------------------------------------------
    |
    | Identifying the service sending the data (e.g., 'WL', 'IAK', 'Buylink').
    |
    */
    'service' => env('FRAUDHUNTER_SERVICE', 'WL'),

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
