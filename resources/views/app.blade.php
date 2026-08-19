<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/project-logo.png') }}?v=1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Template CSS (Conditionally loaded for frontend) -->
        @if(!request()->is('dashboard*') && !request()->is('admin*') && !request()->is('login') && !request()->is('register') && !request()->is('password/*'))
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
            <link rel="stylesheet" href="/frontend/css/frontend.css">
            <link rel="stylesheet" href="/frontend/css/extracted-styles.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        @else
            @vite(['resources/css/app.css'])
        @endif

        <!-- Scripts -->
        @php
            $detectedBasePath = (string) (parse_url(url('/'), PHP_URL_PATH) ?? '');
            $detectedBasePath = '/'.trim($detectedBasePath, '/');

            if ($detectedBasePath === '/') {
                $detectedBasePath = '';
            } else {
                $hasTrailingSlash = str_ends_with($detectedBasePath, '/');
                $segments = array_values(array_filter(
                    explode('/', trim($detectedBasePath, '/')),
                    static fn (string $segment): bool => $segment !== ''
                ));

                if (count($segments) >= 4) {
                    do {
                        $updated = false;
                        $segmentCount = count($segments);

                        for ($len = intdiv($segmentCount, 2); $len >= 2; $len--) {
                            $head = array_slice($segments, 0, $len);
                            $next = array_slice($segments, $len, $len);

                            if ($head === $next && in_array('public', $head, true)) {
                                $segments = array_merge($head, array_slice($segments, $len * 2));
                                $updated = true;
                                break;
                            }
                        }
                    } while ($updated);
                }

                $detectedBasePath = '/'.implode('/', $segments);

                if ($detectedBasePath !== '/' && $hasTrailingSlash) {
                    $detectedBasePath .= '/';
                }
            }
        @endphp
        <script>
            window.__APP_BASE_PATH__ = @json($detectedBasePath);
        </script>
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        @if(!request()->is('dashboard*') && !request()->is('admin*') && !request()->is('login') && !request()->is('register') && !request()->is('password/*'))
            <script src="/frontend/js/main.js"></script>
        @endif
    </body>
</html>
