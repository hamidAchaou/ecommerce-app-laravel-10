@props([
    'href' => null,
    'color' => 'green',
    'icon' => null,
    'outline' => false,
])

@php
    $baseClasses =
        'inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2';
    // Outline colors
    $outlineColors = [
        'gray' => 'border-2 border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white focus:ring-gray-500',
        'yellow' =>
            'border-2 border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-white focus:ring-yellow-500',
        'red' => 'border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white focus:ring-red-500',
        'green' => 'border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white focus:ring-green-500',
        'blue' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-500',
    ];

    // Filled colors
    $filledColors = [
        'gray' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        'yellow' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
        'red' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'green' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'blue' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    ];

    // Outline button classes
    $outlineClasses = $outline
        ? "border-2 border-{$color}-600 text-{$color}-600 hover:bg-{$color}-600 hover:text-white focus:ring-{$color}-500"
        : $filledColors[$color];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $outlineClasses"]) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="submit" {{ $attributes->merge(['class' => "$baseClasses $outlineClasses"]) }}>
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </button>
@endif
