<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col md:flex-row">
        {{-- Sidebar visible only if user is authenticated --}}
        @auth
            @include('components.sidebar')
        @endauth
    
        <div class="flex-1 flex flex-col">
            {{-- Navbar --}}
            @include('components.navbar')
    
            {{-- Content --}}
            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
    
                @hasSection('header')
                    <h1 class="text-2xl font-semibold mb-4">@yield('header')</h1>
                @endif
    
                @yield('content')
            </main>
        </div>
    </div>
    
</body>
</html>
