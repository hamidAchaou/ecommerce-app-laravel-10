<?php

namespace App\View\Components\Frontend\Product;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductDescription extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.frontend.product.product-description');
    }
}
