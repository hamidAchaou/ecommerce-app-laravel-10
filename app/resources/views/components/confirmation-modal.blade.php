@props([
    'id',
    'route',
    'title',
    'message',
    'confirmText' => 'Confirm',
    'buttonClass' => 'inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700',
])

<div x-data="{ open: false }" x-cloak>
    <!-- Trigger Button -->
    <button type="button" @click="open = true" class="{{ $buttonClass }}"
            aria-label="Open confirmation modal for {{ $title }}">
        {{ $slot }}
    </button>

    <!-- Modal -->
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="transform scale-95"
             x-transition:enter-end="transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="transform scale-100"
             x-transition:leave-end="transform scale-95"
             role="dialog" aria-modal="true" aria-labelledby="modal-title-{{ $id }}">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-title-{{ $id }}">{{ $title }}</h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600"
                        aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <p class="text-gray-600 mb-6">{{ $message }}</p>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3">
                <button type="button" @click="open = false"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
                <form action="{{ $route }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        {{ $confirmText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>