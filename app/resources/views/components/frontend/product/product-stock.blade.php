@props(['stock'])

{{-- Stock Status with accessibility --}}
<p class="mt-2 font-semibold"
   aria-live="polite"
   aria-atomic="true"
   itemprop="availability"
   itemscope
   itemtype="https://schema.org/Product">

    @if($stock > 5)
        <span class="text-morocco-green" itemprop="availability" content="InStock">
            In Stock
        </span>
    @elseif($stock > 0)
        <span class="text-yellow-600" itemprop="availability" content="InStock">
            Only {{ $stock }} left in stock — order soon!
        </span>
    @else
        <span class="text-morocco-red" itemprop="availability" content="OutOfStock">
            Out of Stock
        </span>
    @endif
</p>
