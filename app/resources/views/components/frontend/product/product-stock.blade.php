@props(['stock'])

<p class="mt-2 font-semibold {{ $stock > 5 ? 'text-morocco-green' : 'text-morocco-red' }}">
    @if($stock > 5)
        In Stock
    @elseif($stock > 0)
        Only {{ $stock }} left in stock — order soon!
    @else
        Out of Stock
    @endif
</p>
