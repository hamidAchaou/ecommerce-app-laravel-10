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
                {{-- <svg class="h-8 w-8 text-morocco-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg> --}}
            <img src="{{ asset('assets/images/logo-removebg.webp') }}" alt="ATlasShoop Logo" class="w-12 h-12 mb-6">
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
                            class="relative text-gray-500 hover:text-morocco-blue focus:outline-none focus:ring-2 focus:ring-morocco-blue/20 p-2 rounded-full transition-colors duration-200"
                            aria-label="View cart"
                            aria-expanded="cartOpen"
                            aria-controls="cart-dropdown">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <!-- Cart Count Badge -->
                        <span class="absolute -top-2 -right-2 bg-morocco-red text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center transition-all duration-200"
                              x-show="getCartCount() > 0"
                              x-text="getCartCount()">0</span>
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
                        
                        <!-- Cart Header -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Your Cart</h3>
                                <template x-if="cartItems.length > 0">
                                    <button @click="clearCart()"
                                            class="text-sm text-red-600 hover:text-red-800 font-medium">
                                        Clear All
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Cart Items -->
                        <div class="max-h-64 overflow-y-auto">
                            <template x-if="cartItems.length > 0">
                                <ul class="divide-y divide-gray-100">
                                    <template x-for="item in cartItems" :key="item.id">
                                        <li class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors duration-150">
                                            <img :src="item.image" :alt="item.title" 
                                                 class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                            
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 truncate" x-text="item.title"></h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-sm font-semibold text-morocco-red" x-text="`$${item.price.toFixed(2)}`"></span>
                                                    <div class="flex items-center gap-1">
                                                        <button @click="updateQuantity(item.id, item.quantity - 1)"
                                                                class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-morocco-red border border-gray-300 rounded"
                                                                :disabled="item.quantity <= 1">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                            </svg>
                                                        </button>
                                                        <span class="text-xs font-medium px-2" x-text="item.quantity"></span>
                                                        <button @click="updateQuantity(item.id, item.quantity + 1)"
                                                                class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-morocco-red border border-gray-300 rounded">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button @click="removeFromCart(item.id)"
                                                    class="text-gray-400 hover:text-red-500 transition-colors duration-150 p-1"
                                                    aria-label="Remove item from cart">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            
                            <template x-if="cartItems.length === 0">
                                <div class="p-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-sm">Your cart is empty</p>
                                </div>
                            </template>
                        </div>

                        <!-- Cart Footer -->
                        <template x-if="cartItems.length > 0">
                            <div class="p-4 border-t border-gray-100 bg-gray-50">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-medium text-gray-900">Total</span>
                                    <span class="text-lg font-bold text-morocco-red" x-text="`$${getCartTotal().toFixed(2)}`"></span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ url('/cart') }}"
                                       @click="cartOpen = false"
                                       class="block text-center px-3 py-2 bg-white border border-morocco-red text-morocco-red rounded-lg font-medium hover:bg-morocco-red hover:text-white transition-all duration-200 text-sm">
                                        View Cart
                                    </a>
                                    <a href="{{ url('/checkout') }}"
                                       @click="cartOpen = false"
                                       class="block text-center px-3 py-2 bg-morocco-red text-white rounded-lg font-medium hover:bg-morocco-blue transition-all duration-200 text-sm">
                                        Checkout
                                    </a>
                                </div>
                            </div>
                        </template>
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
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black/5 z-50"
                            id="user-menu">
                            <li>
                                <a href="{{ route('profile.show') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/orders') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    My Orders
                                </a>
                            </li>
                            <li>
                                <hr class="my-1">
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-150">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                           class="px-4 py-2 text-sm font-medium text-morocco-blue border border-morocco-blue rounded-full hover:bg-morocco-blue/10 focus:outline-none focus:ring-2 focus:ring-morocco-blue/20 transition-all duration-200">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="px-4 py-2 text-sm font-medium text-white bg-morocco-red rounded-full hover:bg-morocco-blue focus:outline-none focus:ring-2 focus:ring-morocco-red/20 transition-all duration-200">
                                Register
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="sm:hidden" x-data="{ open: false }">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-full text-gray-500 hover:text-morocco-blue hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-morocco-red transition-all duration-200"
                        aria-label="Toggle mobile menu"
                        aria-expanded="open">
                    <svg class="h-6 w-6" :class="{ 'hidden': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{ 'hidden': !open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Mobile Menu -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="absolute top-16 left-0 right-0 bg-white shadow-lg border-t border-gray-100 z-40">
                    <ul class="py-2 space-y-1">
                        @foreach ($navLinks as $link)
                            <li>
                                <a href="{{ url($link['route']) }}"
                                   @click="open = false"
                                   class="{{ request()->is($link['match']) ? 'bg-morocco-red/10 text-morocco-red border-r-2 border-morocco-red' : 'text-gray-600 hover:bg-gray-50 hover:text-morocco-blue' }} block pl-4 pr-4 py-3 text-base font-medium transition-all duration-200">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                        
                        <!-- Mobile Auth Links -->
                        @guest
                            <li class="border-t border-gray-100 pt-2 mt-2">
                                <a href="{{ route('login') }}"
                                   @click="open = false"
                                   class="block pl-4 pr-4 py-3 text-base font-medium text-morocco-blue hover:bg-gray-50 transition-all duration-200">
                                    Login
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li>
                                    <a href="{{ route('register') }}"
                                       @click="open = false"
                                       class="block pl-4 pr-4 py-3 text-base font-medium text-white bg-morocco-red hover:bg-morocco-blue transition-all duration-200">
                                        Register
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="border-t border-gray-100 pt-2 mt-2">
                                <a href="{{ route('profile.show') }}"
                                   @click="open = false"
                                   class="flex items-center pl-4 pr-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200">
                                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/orders') }}"
                                   @click="open = false"
                                   class="flex items-center pl-4 pr-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200">
                                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    My Orders
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();"
                                   @click="open = false"
                                   class="flex items-center pl-4 pr-4 py-3 text-base font-medium text-red-600 hover:bg-red-50 transition-all duration-200">
                                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </a>
                                <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>