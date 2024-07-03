<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        {{-- Favicons --}}
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/favicons/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicons/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('/favicons/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('/favicons/safari-pinned-tab.svg') }}" color="#5bbad5">

        {{-- For Ngrok --}}
        {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}

        @php
        // This is needed so that blade evaluates env variables properly eg. env('VITE_SENSES_CLIENT') would return "$(env('SENSES_CLIENT')"

        $vite = [
            'resources/css/senses.css',
            'resources/js/senses.js',

            // "clients/" . env('SENSES_CLIENT') . "/Resources/css/client.css",
        ];

        @endphp

        @vite($vite)

        {{-- <link rel="stylesheet" href="{{ mix('clients/' . config('senses.client', 'base') . '/css/client.css') }}"> --}}
        {{-- <script src="{{ mix('clients/' . config('senses.client', 'base').'/js/client.js') }}" defer></script> --}}

        <meta http-equiv="refresh" content="{{ (config('session.lifetime') * 60) - 5 }}">
    </head>

    <body class="@yield('body-class') bg-gradient-to-br from-sky-100 dark:from-sky-50 via-yellow-50 to-primary-200 dark:via-zinc-50 dark:to-primary-50 h-screen guest-page" :class="window.getCookie('theme')">
        <div id="{{ env('SENSES_CLIENT') }}" />

        <div id="app">
            @yield('content')
        </div>
    </body>
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            // Device is in dark mode
            document.documentElement.classList.add('nightwind', 'dark');
        }
    </script>
</html>
