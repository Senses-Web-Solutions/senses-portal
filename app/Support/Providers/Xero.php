<?php

namespace App\Support\Providers;

use Calcinai\OAuth2\Client\Provider\Xero as ProviderXero;

class Xero
{
    // Hold the class instance.
    private static $instance = null;

    // The constructor is private
    // to prevent initiation with outer code.
    private function __construct()
    {
        self::$instance = new ProviderXero([
            'clientId' => env('XERO_CLIENT_ID'),
            'clientSecret' => env('XERO_CLIENT_SECRET'),
            'redirectUri' => env('XERO_REDIRECT_URI'),
        ]);
    }

    // The object is created from within the class itself
    public static function getInstance(): ProviderXero
    {
        if (self::$instance == null) {
            self::$instance = new ProviderXero([
                'clientId' => env('XERO_CLIENT_ID'),
                'clientSecret' => env('XERO_CLIENT_SECRET'),
                'redirectUri' => env('XERO_REDIRECT_URI'),
            ]);
        }

        return self::$instance;
    }
}
