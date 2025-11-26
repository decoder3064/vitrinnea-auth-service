<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for authenticating external services that consume this
    | authentication microservice. Used for service-to-service communication.
    |
    */

    // API Key para comunicación entre servicios
    'api_key' => env('AUTH_API_KEY'),

    // API Secret para comunicación entre servicios
    'api_secret' => env('AUTH_API_SECRET'),

];
