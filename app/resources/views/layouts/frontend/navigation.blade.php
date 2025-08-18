@php
    $navLinks = [
        ['label' => 'Home', 'route' => '/', 'match' => '/'],
        ['label' => 'Products', 'route' => '/products', 'match' => 'products*'],
        ['label' => 'Categories', 'route' => '/categories', 'match' => 'categories*'],
        ['label' => 'About', 'route' => '/about', 'match' => 'about'],
        ['label' => 'Contact', 'route' => '/contact', 'match' => 'contact'],
    ];

    $activeClasses = 'border-red-500 text-gray-900';
    $inactiveClasses = 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700';
@endphp


<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-sm shadow-sm" aria-label="Main Navigation" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex-shrink-0 flex items-center" aria-label="Home">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </a>

            <!-- Centered Links -->
            <ul class="hidden sm:flex sm:space-x-8 mx-auto">
                @foreach ($navLinks as $link)
                    <li>
                        <a href="{{ url($link['route']) }}"
                           class="{{ request()->is($link['match']) ? $activeClasses : $inactiveClasses }}
                                  inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>            

            <!-- Right Section: Cart + Auth -->
            <div class="flex items-center space-x-4">
                <!-- Cart Icon -->
                <a href="{{ url('/cart') }}" class="relative text-gray-500 hover:text-gray-700" aria-label="Cart">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span
                        class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </a>

                <!-- Auth -->
                @auth
                    <div class="relative" x-data="{ dropdown: false }">
                        <button @click="dropdown = !dropdown"
                            class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            aria-label="User menu">
                            @if (auth()->user()->profile_photo_path)
                                <img class="h-8 w-8 rounded-full object-cover"
                                     src="{{ asset(auth()->user()->profile_photo_path) }}"
                                     alt="{{ auth()->user()->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                            @endif
                        </button>

                        <ul x-show="dropdown" @click.away="dropdown=false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                            <li>
                                <a href="{{ route('profile.show') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                   <i class="fas fa-user-circle mr-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                   <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="flex space-x-2">
                        <x-button.primary-button href="{{ route('login') }}" color="blue" outline>
                            Login
                        </x-button.primary-button>

                        @if (Route::has('register'))
                            <x-button.primary-button href="{{ route('register') }}" color="red">
                                Register
                            </x-button.primary-button>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" :class="{ 'hidden': open, 'block': !open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{ 'hidden': !open, 'block': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
