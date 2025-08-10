@props([
    'href' => null,
    'color' => 'green',
    'icon' => null,
])

@if($href)
    <a href="{{ $href }}" 
       {{ $attributes->merge(['class' => "inline-flex items-center gap-2 bg-{$color}-600 text-white px-4 py-2 rounded-md hover:bg-{$color}-700 transition"]) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="submit"
       {{ $attributes->merge(['class' => "inline-flex items-center gap-2 bg-{$color}-600 text-white px-4 py-2 rounded-md hover:bg-{$color}-700 transition"]) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </button>
@endif
