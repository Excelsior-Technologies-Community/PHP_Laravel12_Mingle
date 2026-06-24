

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @php
    
        $themeColors = auth()->check()
            ? auth()->user()->themeColors()
            : (new \App\Models\User())->themeColors();
    @endphp

    <style>
        :root {
            --color-bg: {{ $themeColors['bg'] }};
            --color-surface: {{ $themeColors['surface'] }};
            --color-text: {{ $themeColors['text'] }};
            --color-accent: {{ $themeColors['accent'] }};
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-text);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen" style="background-color: var(--color-bg);">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="shadow" style="background-color: var(--color-surface);">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>