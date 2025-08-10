@props([
    'href',
    'color' => 'gray',
])

@php
    $colors = [
        'gray' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
        'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    ];
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => "inline-flex items-center gap-2 text-white px-5 py-2.5 rounded-md focus:outline-none focus:ring-2 transition {$colors[$color]}"
   ]) }}>
    {{ $slot }}
</a>
