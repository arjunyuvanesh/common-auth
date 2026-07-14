<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Common Auth Route Prefix
    |--------------------------------------------------------------------------
    |
    | This value determines the prefix used for all routes registered by
    | the common-auth package. You can change this to suit your app.
    |
    */
    'route_prefix' => 'common-auth',

    /*
    |--------------------------------------------------------------------------
    | Configurable Registration Fields
    |--------------------------------------------------------------------------
    |
    | Here you can define exactly which fields are required when a user registers,
    | along with their validation rules. The package will dynamically read this
    | to validate incoming registration requests.
    |
    | Examples:
    | - For Project A: 'name' => 'required|string', 'email' => 'required|email'
    | - For Project C: 'username' => 'required|string', 'mobile' => 'required'
    |
    */
    'registration_fields' => [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:common_auth_users',
        'password' => 'required|string|min:8|confirmed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Role
    |--------------------------------------------------------------------------
    |
    | When a new user registers through the package, they will automatically
    | be assigned this role using Spatie Laravel Permission.
    |
    */
    'default_role' => 'user',

];
