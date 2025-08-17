@props(['title', 'subtitle' => null])

<div class="text-center mb-12">
    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
        {{ $title }}
    </h2>
    @if($subtitle)
        <p class="mt-2 text-gray-500 text-lg sm:text-xl">
            {{ $subtitle }}
        </p>
    @endif
</div>
