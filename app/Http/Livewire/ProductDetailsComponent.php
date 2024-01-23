<?php
// CartComponent.php

namespace App\Http\Livewire;


use Livewire\Component;

class ProductDetailsComponent extends Component
{
    public $selectedProduct;

    public function render()
    {
        return view('livewire.product-details-component');
    }

    public function addToCart()
    {

        // Lógica para adicionar o produto ao carrinho
        $this->emit('addToCart', $this->selectedProduct);
    }
}
