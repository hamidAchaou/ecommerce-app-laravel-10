@php
$menuGroups = [
    'general' => [
        'label' => __('General'),
        'icon' => 'fas fa-home',
        'items' => [
            'dashboard' => [
                'label' => __('Dashboard'),
                'route' => 'admin.dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'active' => request()->routeIs('admin.dashboard'),
            ],
        ],
    ],
    'catalog' => [
        'label' => __('Catalog'),
        'icon' => 'fas fa-shopping-bag',
        'items' => [
            'products' => [
                'label' => __('Products'),
                'route' => 'admin.products.index',
                'icon' => 'fas fa-box',
                'active' => request()->routeIs('admin.products.*'),
            ],
            'categories' => [
                'label' => __('Categories'),
                'route' => 'admin.categories.index',
                'icon' => 'fas fa-folder-open',
                'active' => request()->routeIs('admin.categories.*'),
            ],
        ],
    ],
    'orders' => [
        'label' => __('Orders'),
        'icon' => 'fas fa-shopping-cart',
        'items' => [
            'all_orders' => [
                'label' => __('All Orders'),
                'route' => 'admin.orders.index',
                'icon' => 'fas fa-list',
                'active' => request()->routeIs('admin.orders.*'),
            ],
            'pending_orders' => [
                'label' => __('Pending Orders'),
                'route' => 'admin.orders.pending',
                'icon' => 'fas fa-clock',
                'active' => request()->routeIs('admin.orders.pending'),
            ],
        ],
    ],
    'access_control' => [
        'label' => __('Access Control'),
        'icon' => 'fas fa-user-shield',
        'items' => [
            'users' => [
                'label' => __('Users'),
                'route' => 'admin.users.index',
                'icon' => 'fas fa-user',
                'active' => request()->routeIs('admin.users.*'),
            ],
            'roles' => [
                'label' => __('Roles'),
                'route' => 'admin.roles.index',
                'icon' => 'fas fa-user-tag',
                'active' => request()->routeIs('admin.roles.*'),
            ],
            'permissions' => [
                'label' => __('Permissions'),
                'route' => 'admin.permissions.index',
                'icon' => 'fas fa-key',
                'active' => request()->routeIs('admin.permissions.*'),
            ],
        ],
    ],
];
@endphp

<aside class="bg-white shadow-md w-64 hidden md:block h-screen sticky top-0"
       aria-label="Sidebar Navigation"
       x-data="{ openGroups: {} }">

    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('assets/images/logo2.png') }}"
                 alt="{{ config('app.name', 'Admin') }} Logo"
                 class="h-10 w-auto object-contain">
            <span class="text-indigo-600 text-2xl font-bold">Hamido</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="mt-6">
        @foreach($menuGroups as $groupKey => $group)
            @php
                $visibleItems = collect($group['items'])->filter(
                    fn($item) => empty($item['permission']) || auth()->user()->can($item['permission'])
                );
                if ($visibleItems->isEmpty()) continue;

                $hasActiveItem = $visibleItems->contains(fn($item) => $item['active']);
            @endphp

            <div class="mb-6 px-4"
                 x-data="{ isOpen: {{ $hasActiveItem ? 'true' : 'false' }} }"
                 x-init="openGroups['{{ $groupKey }}'] = isOpen">

                <button type="button"
                        @click="isOpen = !isOpen; openGroups['{{ $groupKey }}'] = isOpen"
                        :aria-expanded="isOpen.toString()"
                        class="flex items-center justify-between w-full text-gray-600 hover:text-indigo-600 uppercase text-xs font-semibold tracking-wide mb-3 focus:outline-none">
                    <span class="flex items-center gap-2">
                        <i class="{{ $group['icon'] }} w-5 text-center"></i>
                        {{ $group['label'] }}
                    </span>
                    <svg class="w-4 h-4 transform transition-transform duration-200"
                         :class="{ 'rotate-90': isOpen }"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <ul x-show="isOpen" x-collapse class="space-y-1">
                    @foreach($visibleItems as $itemKey => $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 px-4 py-2 rounded-md transition
                                      {{ $item['active'] ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}"
                               aria-current="{{ $item['active'] ? 'page' : 'false' }}">
                                <i class="{{ $item['icon'] }} w-5 text-center"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>
</aside>
