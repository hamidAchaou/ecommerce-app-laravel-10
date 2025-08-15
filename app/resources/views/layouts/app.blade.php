<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', config('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-100 font-sans antialiased">

    @auth
        @if (auth()->user()->hasRole('admin'))
            {{-- Spatie role check --}}
            @include('layouts.navbar')
            <div class="flex min-h-screen">
                @include('layouts.sidebar')
                <main class="flex-1 p-6">
                    @yield('content')
                </main>
            </div>
        @else
            @include('layouts.navigation')
            <main class="p-6 max-w-7xl mx-auto">
                @yield('content')
            </main>
        @endif
    @else
        @include('layouts.navigation')
        <main class="p-6 max-w-7xl mx-auto">
            @yield('content')
        </main>
    @endauth

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow"
            role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @stack('scripts')
</body>

</html>
