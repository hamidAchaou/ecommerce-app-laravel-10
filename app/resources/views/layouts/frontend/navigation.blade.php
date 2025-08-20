@php
    $navLinks = [
        ['label' => 'Home', 'route' => '/', 'match' => '/'],
        ['label' => 'Products', 'route' => '/products', 'match' => 'products*'],
        ['label' => 'Categories', 'route' => '/categories', 'match' => 'categories*'],
        ['label' => 'About', 'route' => '/about', 'match' => 'about'],
        ['label' => 'Contact', 'route' => '/contact', 'match' => 'contact'],
    ];

    $activeClasses = 'border-morocco-red text-gray-900';
    $inactiveClasses = 'border-transparent text-gray-500 hover:border-morocco-yellow hover:text-gray-700';
@endphp

<nav 
    class="sticky top-0 z-50 bg-white/95 backdrop-blur-md shadow-sm"
    aria-label="Main Navigation"
    x-data="cart()" 
    x-init="init()"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex-shrink-0 flex items-center" aria-label="Home">
                <svg class="h-8 w-8 text-morocco-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </a>

            <!-- Centered Links -->
            <ul class="hidden sm:flex sm:space-x-8 mx-auto">
                @foreach ($navLinks as $link)
                    <li>
                        <a href="{{ url($link['route']) }}"
                           class="{{ request()->is($link['match']) ? $activeClasses : $inactiveClasses }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <!-- Right Section: Cart + Auth -->
            <div class="flex items-center space-x-4">
                <!-- Cart Icon with Dropdown -->
                <div class="relative" x-data="{ cartOpen: false }">
                    <button @click="cartOpen = !cartOpen"
                            class="relative text-gray-500 hover:text-morocco-blue focus:outline-none focus:ring-2 focus:ring-morocco-blue/20 p-2 rounded-full"
                            aria-label="View cart"
                            aria-expanded="cartOpen"
                            aria-controls="cart-dropdown">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-morocco-red text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
                              x-text="cartItems?.length || 0">0</span>
                    </button>

                    <!-- Cart Dropdown -->
                    <div x-show="cartOpen"
                         @click.away="cartOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg ring-1 ring-black/5 z-50 overflow-hidden"
                         id="cart-dropdown"
                         role="region"
                         aria-label="Shopping cart">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Your Cart</h3>
                        </div>
                        <ul class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                            <template x-if="cartItems?.length > 0">
                                <template x-for="item in cartItems" :key="item.id">
                                    <li class="flex items-center gap-4 p-4 hover:bg-gray-50">
                                        <img :src="item.image" :alt="item.title" class="w-16 h-16 object-cover rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900 line-clamp-1" x-text="item.title"></h4>
                                            <p class="text-sm text-gray-500" x-text="`$${item.price.toFixed(2)} x ${item.quantity}`"></p>
                                        </div>
                                        <button @click="removeFromCart(item.id)"
                                                class="text-gray-400 hover:text-morocco-red"
                                                aria-label="Remove item from cart">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </li>
                                </template>
                            </template>
                            <template x-if="!cartItems || cartItems.length === 0">
                                <li class="p-4 text-center text-gray-500 text-sm">Your cart is empty</li>
                            </template>
                        </ul>
                        <div class="p-4 border-t border-gray-100">
                            <div class="flex justify-between text-sm font-medium text-gray-900">
                                <span>Total</span>
                                <span x-text="cartItems ? `$${cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0).toFixed(2)}` : '$0.00'"></span>
                            </div>
                            <a href="{{ url('/cart') }}"
                               class="mt-4 block w-full bg-morocco-red text-white text-center py-2 rounded-full font-semibold hover:bg-morocco-blue transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-morocco-blue/20">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Auth -->
                @auth
                    <div class="relative" x-data="{ dropdown: false }">
                        <button @click="dropdown = !dropdown"
                                class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-morocco-red"
                                aria-label="User menu"
                                aria-expanded="dropdown"
                                aria-controls="user-menu">
                            @if (auth()->user()->profile_photo_path)
                                <img class="h-8 w-8 rounded-full object-cover"
                                     src="{{ asset(auth()->user()->profile_photo_path) }}"
                                     alt="{{ auth()->user()->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </button>

                        <ul x-show="dropdown"
                            @click.away="dropdown = false"
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black/5 z-50"
                            id="user-menu">
                            <li>
                                <a href="{{ route('profile.show') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="flex space-x-2">
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 text-sm font-medium text-morocco-blue border border-morocco-blue rounded-full hover:bg-morocco-blue/10 focus:outline-none focus:ring-2 focus:ring-morocco-blue/20">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="px-4 py-2 text-sm font-medium text-white bg-morocco-red rounded-full hover:bg-morocco-blue focus:outline-none focus:ring-2 focus:ring-morocco-red/20">
                                Register
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="sm:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-full text-gray-500 hover:text-morocco-blue hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-morocco-red"
                        aria-label="Toggle mobile menu"
                        aria-expanded="open">
                    <svg class="h-6 w-6" :class="{ 'hidden': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{ 'hidden': !open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" class="sm:hidden" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            <ul class="pt-2 pb-4 space-y-1">
                @foreach ($navLinks as $link)
                    <li>
                        <a href="{{ url($link['route']) }}"
                           class="{{ request()->is($link['match']) ? 'bg-morocco-red/10 text-morocco-red' : 'text-gray-600 hover:bg-gray-50 hover:text-morocco-blue' }} block pl-4 pr-4 py-2 text-base font-medium">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>