<div class="min-h-screen flex flex-col">
    {{-- Navigation --}}
    @include('layouts.frontend.navigation')
    
    {{-- Main content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.frontend.footer')

    {{-- Flash messages --}}
    <x-shared.flash-messages />
</div>
