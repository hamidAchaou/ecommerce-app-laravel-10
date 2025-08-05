@php
    $menuGroups = [
        [
            'label' => 'Gestion des Utilisateurs',
            'icon' => 'fas fa-users',
            'items' => [
                [
                    'label' => 'Utilisateurs',
                    'route' => 'admin.users.index',
                    'icon' => 'fas fa-user',
                    'activePattern' => 'admin.users.*',
                ],
            ],
        ],
        [
            'label' => 'Gestion des Rôles & Permissions',
            'icon' => 'fas fa-lock',
            'items' => [
                [
                    'label' => 'Rôles',
                    'route' => 'admin.roles.index',
                    'icon' => 'fas fa-users-cog',
                    'activePattern' => 'admin.roles.*',
                ],
                [
                    'label' => 'Permissions',
                    'route' => 'admin.permissions.index',
                    'icon' => 'fas fa-shield-alt',
                    'activePattern' => 'admin.permissions.*',
                ],
            ],
        ],
    ];
@endphp

<aside 
    class="bg-white shadow-md w-64 hidden md:block h-screen sticky top-0" 
    aria-label="Sidebar Navigation"
    x-data="{ openGroup: null }"
>
    <div class="p-6 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 text-2xl font-bold flex items-center space-x-2">
            <i class="fas fa-chart-line"></i>
            <span>Admin</span>
        </a>
    </div>

    <nav class="mt-6" role="navigation" aria-label="Main Navigation">
        @foreach($menuGroups as $index => $group)
            @php
                // Check if any child is active to open dropdown by default
                $isActiveGroup = collect($group['items'])->contains(function($item) {
                    return request()->routeIs($item['activePattern']);
                });
            @endphp

            <div class="mb-6 px-4" x-data="{ isOpen: {{ $isActiveGroup ? 'true' : 'false' }} }">
                <button 
                    type="button" 
                    @click="isOpen = !isOpen" 
                    aria-expanded="false"
                    class="flex items-center justify-between w-full text-gray-500 uppercase text-xs font-semibold tracking-wide mb-3 focus:outline-none"
                    :aria-expanded="isOpen.toString()"
                >
                    <span class="flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition font-semibold">
                        <i class="{{ $group['icon'] }}"></i>
                        {{ $group['label'] }}
                    </span>
                    <svg 
                        class="w-4 h-4 transform transition-transform duration-300" 
                        :class="{ 'rotate-90': isOpen }" 
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M9 18l6-6-6-6" />
                    </svg>
                </button>

                <ul 
                    x-show="isOpen" 
                    x-collapse 
                    class="space-y-1"
                    @click.outside="isOpen = false"
                >
                    @foreach($group['items'] as $item)
                        @php
                            $isActive = request()->routeIs($item['activePattern']);
                        @endphp
                        <li>
                            <a 
                                href="{{ route($item['route']) }}" 
                                class="flex items-center gap-3 px-4 py-2 rounded-md text-gray-700 hover:bg-indigo-100 hover:text-indigo-700 transition
                                    {{ $isActive ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}"
                                aria-current="{{ $isActive ? 'page' : '' }}"
                            >
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
