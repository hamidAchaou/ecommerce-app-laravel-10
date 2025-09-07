@props([
    'active' => false,
    'type' => 'button',
    'ariaLabel' => '',
])

<button type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center p-2 rounded-full transition focus:outline-none focus:ring-2 focus:ring-offset-2 ' 
            . ($active 
                ? 'bg-red-600 text-white border-red-600 hover:bg-red-700' 
                : 'bg-white text-red-600 border border-red-600 hover:bg-red-50')
    ]) }}
    aria-label="{{ $ariaLabel }}"
>
    {{ $slot }}
</button>
