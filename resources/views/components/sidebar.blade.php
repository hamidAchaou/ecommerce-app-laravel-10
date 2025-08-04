<aside class="bg-white shadow-md w-64 hidden md:block h-screen sticky top-0">
    <div class="p-6">
        <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 text-2xl font-bold">
            <i class="fas fa-chart-line mr-2"></i> Admin
        </a>
    </div>
    <nav class="mt-6">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-100 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.roles.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-100 rounded {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                    <i class="fas fa-users-cog mr-2"></i> Roles
                </a>
            </li>
            <li>
                <a href="{{ route('admin.permissions.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-100 rounded {{ request()->routeIs('admin.permissions.*') ? 'bg-indigo-100 text-indigo-700' : '' }}">
                    <i class="fas fa-shield-alt mr-2"></i> Permissions
                </a>
            </li>
        </ul>
    </nav>
</aside>
