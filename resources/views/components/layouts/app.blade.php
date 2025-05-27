<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Statusphere Laravel edition') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-white dark:bg-gray-800 dark:text-white">
    <header class="py-2 bg-blue-500 text-white text-center">
        This is the Laravel version of <a href="https://atproto.com/guides/applications" target="_blank" class="underline">Statusphere</a>. <a href="https://github.com/invokable/statusphere" target="_blank" class="underline">GitHub</a>
    </header>

    <main class="max-w-2xl mx-auto px-4">
        <div class="text-center my-5">
            <h1 class="text-5xl font-black"><a href="{{ route('welcome') }}">Statusphere</a></h1>
            <p class="my-3 text-sm">Set your status on the Atmosphere.</p>
        </div>

        {{ $slot }}
    </main>
</div>
</body>
</html>
