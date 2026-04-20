<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-fintrack.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>[x-cloak] { display: none !important; }</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 min-h-screen flex items-center justify-center font-sans antialiased">
        <div class="w-full md:w-2/6 px-4 mt-8">

            <!-- Topo: Logo e Títulos Globais -->
            <div class="flex flex-col items-center justify-center text-center space-y-1">
                <a href="/">
                    <div class="inline-flex items-center justify-center p-2 w-24 h-24 bg-white rounded-full mb-4 shadow-lg hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('images/logo-fintrack.png') }}" alt="FinTrack Logo" class="w-full h-full object-contain drop-shadow-md">
                    </div>
                </a>
                <h1 class="text-5xl font-bold text-white tracking-tight">FinTrack</h1>
                <p class="text-blue-100 text-base font-medium">Gerencie suas finanças de forma simples</p>
            </div>

            <!-- Aqui é onde o cartão de Login ou Registro será injetado -->
            <div class="mt-4">
                {{ $slot }}
            </div>

            <!-- Footer Global -->
            <p class="text-center text-blue-200 text-sm mt-8 mb-8 opacity-80">
                FinTrack © {{ date('Y') }}
            </p>

        </div>
    </body>
</html>
