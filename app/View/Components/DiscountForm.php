<?php

namespace App\View\Components;

use App\Entities\Discount;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class DiscountForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public readonly ?Discount  $discount,
        public readonly Collection $products,
    ) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.discount-form', [
            'discount' => $this->discount,
            'products' => $this->products,
        ]);
    }
}
