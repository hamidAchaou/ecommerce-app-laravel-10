@extends('layouts.frontend')

@section('title', $product->name . ' - ' . config('app.name'))

@section('content')
<div class="container py-5">

    {{-- Product Section --}}
    <div class="row">
        {{-- Left: Images --}}
        <div class="col-md-5">
            <div class="border p-3 bg-white rounded">
                <img id="mainImage" src="{{ asset($product->mainImage->path ?? 'images/no-image.png') }}" 
                     class="img-fluid w-100" alt="{{ $product->name }}">
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach($product->images as $image)
                        <img src="{{ asset($image->path) }}" 
                             class="img-thumbnail border" 
                             style="width: 70px; height: 70px; cursor:pointer;"
                             onclick="document.getElementById('mainImage').src=this.src">
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Product Details --}}
        <div class="col-md-7">
            <h2 class="fw-bold">{{ $product->name }}</h2>
            
            {{-- Ratings & Reviews --}}
            <div class="d-flex align-items-center mb-2">
                <div class="text-warning">
                    @for ($i = 0; $i < 5; $i++)
                        <i class="fa{{ $i < $product->rating ? 's' : 'r' }} fa-star"></i>
                    @endfor
                </div>
                <span class="ms-2 text-muted">({{ $product->reviews_count ?? 0 }} reviews)</span>
            </div>

            {{-- Price & Discount --}}
            <div class="mb-3">
                @if($product->discount_price)
                    <h3 class="text-danger fw-bold">
                        ${{ number_format($product->discount_price, 2) }}
                        <small class="text-muted text-decoration-line-through ms-2">
                            ${{ number_format($product->price, 2) }}
                        </small>
                        <span class="badge bg-success ms-2">
                            -{{ round((1 - $product->discount_price/$product->price) * 100) }}%
                        </span>
                    </h3>
                @else
                    <h3 class="fw-bold">${{ number_format($product->price, 2) }}</h3>
                @endif
            </div>

            {{-- Stock Status --}}
            <p class="{{ $product->stock > 5 ? 'text-success' : 'text-danger fw-bold' }}">
                @if($product->stock > 5)
                    In Stock
                @elseif($product->stock > 0)
                    Only {{ $product->stock }} left in stock — order soon!
                @else
                    Out of Stock
                @endif
            </p>

            {{-- Delivery Estimate --}}
            <p><i class="fas fa-truck"></i> Free delivery by <strong>{{ now()->addDays(3)->format('l, M d') }}</strong></p>

            {{-- Add to Cart --}}
            <form action="{{ route('cart.store', $product->id) }}" method="POST" class="mb-3">
                @csrf
                <div class="d-flex align-items-center mb-3">
                    <label for="quantity" class="me-2 fw-bold">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" 
                           value="1" min="1" max="{{ $product->stock }}" 
                           class="form-control w-25">
                </div>
                <button type="submit" class="btn btn-warning btn-lg w-100 mb-2">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <button type="button" class="btn btn-danger btn-lg w-100">
                    <i class="fas fa-bolt"></i> Buy Now
                </button>
            </form>

            {{-- Product Description --}}
            <h5>About this item</h5>
            <p>{{ $product->description }}</p>
        </div>
    </div>

    {{-- Related Products --}}
    <div class="mt-5">
        <h4 class="mb-4">Customers also bought</h4>
        <div class="row">
            @foreach($relatedProducts as $related)
                <div class="col-md-3 col-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset($related->mainImage->path ?? 'images/no-image.png') }}" 
                             class="card-img-top" alt="{{ $related->name }}">
                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($related->name, 40) }}</h6>
                            <p class="fw-bold text-danger">${{ number_format($related->price, 2) }}</p>
                            <a href="{{ route('products.show', $related->id) }}" 
                               class="btn btn-outline-primary btn-sm w-100">View</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
