<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-fintrack.png') }}">

        <title>{{ config('app.name', 'FinTrack') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.header')

            <main class="mx-auto max-w-7xl w-full px-3 sm:px-4 lg:px-8 py-4 sm:py-8 pb-[max(1rem,env(safe-area-inset-bottom))]">
                {{ $slot }}
            </main>
        </div>

        <x-toast />
        <x-confirm-dialog />

        {{-- Flash messages --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', message: '{{ addslashes(session("success")) }}' }
                    }));
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'error', message: '{{ addslashes(session("error")) }}' }
                    }));
                });
            </script>
        @endif

        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: '{{ addslashes($errors->first()) }}',
                            duration: 6000
                        }
                    }));
                });
            </script>
        @endif
    </body>
</html>
