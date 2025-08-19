@props(['images', 'title'])

@php
    $primaryImage = $images->where('is_primary', 1)->first() ?? $images->first();
@endphp

<div class="bg-morocco-ivory p-4 rounded-lg shadow-lg">
    <img id="mainImage"
         src="{{ asset('storage/' . ($primaryImage->image_path ?? 'images/no-image.png')) }}"
         alt="{{ $primaryImage->alt_text ?? $title }}"
         class="w-full h-[400px] object-contain rounded-lg mb-4"
         loading="lazy">

    <div class="flex flex-wrap gap-2">
        @foreach ($images as $image)
            <img src="{{ asset('storage/' . $image->image_path) }}"
                 alt="{{ $image->alt_text ?? $title }}"
                 class="w-20 h-20 object-cover rounded-md border cursor-pointer hover:ring-2 hover:ring-morocco-blue transition"
                 loading="lazy"
                 onclick="document.getElementById('mainImage').src=this.src">
        @endforeach
    </div>
</div>
