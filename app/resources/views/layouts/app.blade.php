<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('description', 'Default description')">

    <!-- ✅ Preconnect for better performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- ✅ Google Fonts (no preload warning) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- ✅ App CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body class="min-h-full bg-gray-50 font-sans antialiased">
    @auth
        @if (auth()->user()->hasRole('admin'))
            @include('layouts.admin.admin-wrapper')
        @else
            @include('layouts.frontend.frontend-wrapper')
        @endif
    @else
        @include('layouts.frontend.frontend-wrapper')
    @endauth

    @stack('modals')
    @stack('scripts')
</body>
</html>