<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'To-Do') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: var(--paper-card);">
        <div class="flex items-center gap-2 mb-6">
            <span class="font-semibold text-lg" style="color: var(--ink);">{{ config('app.name', 'To-Do') }}</span>
        </div>

        <div class="w-full sm:max-w-sm px-6 py-8" style="background: var(--paper); border: 1px solid var(--line); border-radius: 8px;">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
