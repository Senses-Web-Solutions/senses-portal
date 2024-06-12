<!doctype html>

<html id='root' lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="{{ (config('session.lifetime') * 60) - 5 }}">

        <title>@yield('title', '') | Senses </title>

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/favicons/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/favicons/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('/favicons/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('/favicons/safari-pinned-tab.svg') }}" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <script type="text/javascript" src="/js/circles.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.0.1/chart.umd.js"></script>


        @vite([
            'resources/css/senses.css',
            'resources/js/senses.js'
        ])

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700;800&display=swap" rel="stylesheet">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script>
            function myToggleFunction() {
                var element = document.getElementById("root");
                element.classList.toggle("dark");
            }
        </script>
    </head>

    <body class="text-base antialiased {{ config('senses.client') }}">
        @if(auth()->check())
            <script>
                window.sensesCurrentUser = {!! $currentUser !!};
                    window.sensesActualCurrentUserID = {!!   auth()->id()  !!};
                    window.sensesClient = "{{ config('senses.client') }}";
                    window.sensesDayModes = {!! json_encode(config('client.scheduler.day_modes')) !!};
                    @php
                        $sensesClientConfig = collect(config('client',[]));
                        $sensesClientConfig = $sensesClientConfig->only([
                            'terminology',
                        ]);
                    @endphp
                    window.sensesClientConfig = @json($sensesClientConfig);
                    window.localDevelopment = @json(config('senses.local_dev', false));
            </script>
        @endif

        {{-- @if (env('APP_ENV') === 'production') --}}
        @if(auth()->user())
            <script type="text/javascript">
                var lz_data = {overwrite: true, 111: '{{auth()->user()->full_name}}'};
            </script>
        @endif

        <script data-livezilla type="text/javascript" id="dda9baeb44c41abd8db8bc37f0176e63" src="https://live.senses.co.uk/script.php?id=dda9baeb44c41abd8db8bc37f0176e63" defer></script>
        {{-- @endif --}}

        <div id="app" class="">
            <Layout>
                <template #sidebar>
                    {{-- <Sidebar></Sidebar> --}}
                </template>

                <template #content>
                    @yield('content')
                </template>
            </Layout>

            <snackbars></snackbars>
            <notifications></notifications>
            <dialogs></dialogs>

            <senses-aside></senses-aside>
            <senses-modal></senses-modal>
        </div>
    </body>
</html>
