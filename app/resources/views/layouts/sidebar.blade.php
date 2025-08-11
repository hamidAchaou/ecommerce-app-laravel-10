@php
    $menuGroups = [
        [
            'label' => 'General',
            'icon' => 'fas fa-home',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'active' => request()->routeIs('admin.dashboard'),
                ],
            ],
        ],
        [
            'label' => 'Catalog',
            'icon' => 'fas fa-shopping-bag',
            'items' => [
                [
                    'label' => 'Products',
                    'route' => 'admin.products.index',
                    'icon' => 'fas fa-box',
                    'active' => request()->routeIs('admin.products.*'),
                ],
                [
                    'label' => 'Categories',
                    'route' => 'admin.categories.index',
                    'icon' => 'fas fa-folder-open',
                    'active' => request()->routeIs('admin.categories.*'),
                ],
            ],
        ],
        [
            'label' => 'Commandes',
            'icon' => 'fas fa-shopping-cart',
            'items' => [
                [
                    'label' => 'Toutes les Commandes',
                    'route' => 'admin.orders.index',
                    'icon' => 'fas fa-list',
                    'active' => request()->routeIs('admin.orders.*'),
                ],
                [
                    'label' => 'Commandes en Attente',
                    'route' => 'admin.orders.pending',
                    'icon' => 'fas fa-clock',
                    'active' => request()->routeIs('admin.orders.pending'),
                ],
            ],
        ],
        [
            'label' => 'Utilisateurs & Accès',
            'icon' => 'fas fa-user-shield',
            'items' => [
                [
                    'label' => 'Utilisateurs',
                    'route' => 'admin.users.index',
                    'icon' => 'fas fa-user',
                    'active' => request()->routeIs('admin.users.*'),
                ],
                [
                    'label' => 'Rôles',
                    'route' => 'admin.roles.index',
                    'icon' => 'fas fa-user-tag',
                    'active' => request()->routeIs('admin.roles.*'),
                ],
                [
                    'label' => 'Permissions',
                    'route' => 'admin.permissions.index',
                    'icon' => 'fas fa-key',
                    'active' => request()->routeIs('admin.permissions.*'),
                ],
            ],
        ],
    ];
@endphp

<aside class="bg-white shadow-md w-64 hidden md:block h-screen sticky top-0" aria-label="Sidebar Navigation"
    x-data="{ openGroup: null }">
    <!-- Logo/Header -->
    <div class="p-6 border-b border-gray-200 flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('assets/images/logo2.png') }}" alt="{{ config('app.name', 'Admin') }} Logo"
                class="h-10 w-auto object-contain" loading="lazy" decoding="async" />
            <span class="text-indigo-600 text-2xl font-bold select-none hidden sm:inline-block">
                Hamido
            </span>
        </a>
    </div>


    <!-- Navigation -->
    <nav class="mt-6" role="navigation" aria-label="Main Navigation">
        @foreach ($menuGroups as $index => $group)
            @php
                // Filter only items the user has permission to see
                $visibleItems = collect($group['items'])->filter(
                    fn($item) => empty($item['permission']) || auth()->user()->can($item['permission']),
                );

                // Skip group if no visible items
                if ($visibleItems->isEmpty()) {
                    continue;
                }

                $groupIsActive = $visibleItems->contains(fn($item) => $item['active']);
            @endphp

            <div class="mb-6 px-4" x-data="{ isOpen: {{ $groupIsActive ? 'true' : 'false' }} }">
                <!-- Group Toggle -->
                <button type="button" @click="isOpen = !isOpen" :aria-expanded="isOpen.toString()"
                    class="flex items-center justify-between w-full text-gray-600 hover:text-indigo-600 uppercase text-xs font-semibold tracking-wide mb-3 focus:outline-none transition">
                    <span class="flex items-center gap-2">
                        <i class="{{ $group['icon'] }}"></i>
                        {{ $group['label'] }}
                    </span>
                    <svg class="w-4 h-4 transform transition-transform duration-300" :class="{ 'rotate-90': isOpen }"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M9 18l6-6-6-6" />
                    </svg>
                </button>

                <!-- Group Items -->
                <ul x-show="isOpen" x-collapse class="space-y-1">
                    @foreach ($visibleItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-md transition
                                    {{ $item['active'] ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}"
                                aria-current="{{ $item['active'] ? 'page' : '' }}">
                                <i class="{{ $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>
</aside>
