@props(['images' => collect(), 'title' => ''])

@php
    use Illuminate\Support\Collection;

    /** @var Collection|\App\Models\ProductImage[]|string $images */

    // Normalize $images
    if (is_string($images)) {
        $images = collect();
    } elseif (is_array($images)) {
        $images = collect($images);
    } elseif (!$images instanceof Collection) {
        $images = collect($images ?? []);
    }

    // Primary or fallback
    $primaryImage = $images->firstWhere('is_primary', 1) ?? $images->first();
    $fallbackImage = 'storage/images/no-image.png';

    $mainImageSrc = $primaryImage 
        ? asset('storage/' . $primaryImage->image_path) 
        : asset($fallbackImage);

    $mainImageAlt = $primaryImage->alt_text ?? $title;
@endphp

<div class="bg-morocco-ivory p-4 rounded-lg shadow-lg">
    {{-- Main Image --}}
    <img 
        id="mainImage"
        src="{{ $mainImageSrc }}"
        alt="{{ $mainImageAlt }}"
        class="w-full h-[400px] object-contain rounded-lg mb-4"
        loading="lazy"
    >

    {{-- Thumbnails --}}
    @if ($images->count() > 1)
        <div class="flex flex-wrap gap-2">
            @foreach ($images as $image)
                <img
                    src="{{ asset('storage/' . $image->image_path) }}"
                    alt="{{ $image->alt_text ?? $title }}"
                    class="w-20 h-20 object-cover rounded-md border cursor-pointer hover:ring-2 hover:ring-morocco-blue transition"
                    loading="lazy"
                    onclick="document.getElementById('mainImage').src=this.src"
                >
            @endforeach
        </div>
    @endif
</div>
