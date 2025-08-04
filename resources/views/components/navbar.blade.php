<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <div>
        <span class="text-xl font-semibold text-gray-800">Admin Panel</span>
    </div>
    
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
            @if(Auth::user()->profile_photo_path)
                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                     alt="Profile"
                     class="w-8 h-8 rounded-full object-cover border border-gray-300" />
            @else
                <i class="fas fa-user-circle text-2xl text-gray-600"></i>
            @endif
            <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Dropdown -->
        <div x-show="open" @click.away="open = false"
             class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-10">
            <a href="{{ route('profile.edit') }}"
               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-user mr-2"></i> Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-red-500 hover:bg-red-100">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>
