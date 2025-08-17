@props([
    'href' => '#',
    'color' => 'gray',
    'outline' => false, 
])

@php
$colors = [
    'gray' => 'bg-gray-600 text-white border-gray-600 hover:bg-gray-700 hover:border-gray-700 focus:ring-gray-500',
    'red' => 'bg-red-600 text-white border-red-600 hover:bg-red-700 hover:border-red-700 focus:ring-red-500',
    'green' => 'bg-green-600 text-white border-green-600 hover:bg-green-700 hover:border-green-700 focus:ring-green-500',
    'blue' => 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 hover:border-blue-700 focus:ring-blue-500',
    'yellow' => 'bg-yellow-600 text-white border-yellow-600 hover:bg-yellow-700 hover:border-yellow-700 focus:ring-yellow-500',
];

// Determine the classes based on the color and outline options
$classes = $outline 
    ? "inline-flex items-center gap-2 px-5 py-2.5 rounded-md border-2 font-medium focus:outline-none focus:ring-2 transition border-{$color}-600 text-{$color}-600 hover:bg-{$color}-600 hover:text-white"
    : "inline-flex items-center gap-2 px-5 py-2.5 rounded-md font-medium focus:outline-none focus:ring-2 transition {$colors[$color]}";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
