<div class="min-h-screen flex flex-col">
    @include('layouts.admin.navbar')

    <x-shared.flash-messages />

    <div class="flex flex-1">
        @include('layouts.admin.sidebar')

        <main class="flex-1 p-6 overflow-x-hidden">
            @yield('content')
        </main>
    </div>

</div>
