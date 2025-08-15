<nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <div>
        <span class="text-xl font-semibold text-gray-800">{{ config('app.name') }} Admin</span>
    </div>
    
    <div class="relative" x-data="{ open: false }">
        @auth
            <button @click="open = !open" 
                    class="flex items-center space-x-2 focus:outline-none"
                    aria-label="User menu"
                    aria-haspopup="true"
                    :aria-expanded="open">
                @if(Auth::user()->profile_photo_path)
                    <img src="{{ Auth::user()->profilePhotoUrl }}" 
                         alt="{{ Auth::user()->name }}"
                         class="w-8 h-8 rounded-full object-cover border border-gray-300">
                @else
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200">
                        <span class="text-gray-600">{{ Auth::user()->initials }}</span>
                    </span>
                @endif
                <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" 
                     :class="{ 'rotate-180': open }" 
                     fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Dropdown -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-10">
                <a href="{{ route('profile.edit') }}"
                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                    <i class="fas fa-user mr-2 w-5 text-center"></i> 
                    <span>Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-red-500 hover:bg-red-100 flex items-center">
                        <i class="fas fa-sign-out-alt mr-2 w-5 text-center"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>