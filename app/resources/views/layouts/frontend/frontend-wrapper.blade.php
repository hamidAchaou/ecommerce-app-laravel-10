<div class="min-h-screen flex flex-col">
    @include('layouts.frontend.navigation')
    
    <main class="flex-1">
        @yield('content')
    </main>
    
    @include('layouts.frontend.footer')
    <x-shared.flash-messages />
</div>