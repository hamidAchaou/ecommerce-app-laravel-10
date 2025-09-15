@props(['description'])

@php
    // Sanitize the description to prevent XSS
    $safeDescription = strip_tags($description, '<p><br><strong><em><ul><ol><li><a>');
@endphp

<section class="prose mt-6 text-gray-700" itemprop="description">
    <h2 class="text-xl font-semibold text-gray-900 mb-2">About this item</h2>
    
    @if($safeDescription)
        {!! $safeDescription !!}
    @else
        <p>No description available for this product.</p>
    @endif
</section>
