<nav class="bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center shadow-sm">
    {{-- Logo + Links --}}
    <div class="flex items-center space-x-6">
        {{-- <a href="{{ route('admin.dashboard') }}">
            <x-application-logo class="h-8 w-auto text-indigo-600" />
        </a> --}}

        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
            {{ __('Dashboard') }}
        </x-nav-link>
    </div>

    {{-- Profile & Dropdown --}}
    <div class="relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center text-sm text-gray-600 hover:text-indigo-600 focus:outline-none">
                    <span>{{ Auth::user()->name }}</span>
                    <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0L5.293 8.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</nav>
