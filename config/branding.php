<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Branding Configuration
    |--------------------------------------------------------------------------
    |
    | These values define the visual identity of the application. They can
    | be customized via the /settings/branding page and are used across
    | the landing page, sidebar, auth pages, and email templates.
    |
    */

    'name' => env('APP_NAME', 'HUB'),

    'description' => env('APP_DESCRIPTION', 'Um ponto de partida seguro e moderno para construir sistemas administrativos com a TALL Stack.'),

    'tagline' => env('APP_TAGLINE', 'Starter Kit Administrativo'),

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | The logo letter is displayed inside a gradient badge on the landing
    | page and sidebar. The gradient colors use Tailwind color names.
    |
    */

    'logo' => [
        'text' => 'H',
        'gradient_from' => 'red-500',
        'gradient_to' => 'orange-500',
        'gradient_from_hex' => '#ef4444',
        'gradient_to_hex' => '#f97316',
    ],

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    */

    'favicon' => [
        'ico' => '/favicon.ico',
        'svg' => '/favicon.svg',
        'apple' => '/apple-touch-icon.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI
    |--------------------------------------------------------------------------
    |
    */

    'ui' => [
        'animations' => true,
    ],

];
