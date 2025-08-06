<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Login to your account">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-12 sm:py-0">
            <!-- Header with Logo -->
            <header class="mb-8">
                <a href="/" class="flex items-center justify-center">
                    <x-application-logo class="w-24 h-24 fill-current text-gray-700 dark:text-gray-300 transition-transform duration-300 hover:scale-105" />
                </a>
            </header>

            <!-- Main Content -->
            <main class="w-full max-w-md space-y-6">
                @yield('content')
                {{-- <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 sm:p-8 overflow-hidden transform transition-all duration-300 hover:shadow-2xl">
                </div> --}}
            </main>            

            <!-- Footer (Optional) -->
            <footer class="mt-8 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </footer>
        </div>
    </body>
</html>