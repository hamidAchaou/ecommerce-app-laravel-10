@if(session()->has('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         @click="show = false"
         class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg cursor-pointer flex items-start max-w-xs"
         role="alert">
        <div class="flex-shrink-0 pt-0.5">
            <i class="fas fa-check-circle text-green-500"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session()->has('error'))
    <!-- Similar structure for error messages -->
@endif