<?php

namespace App\Http\Livewire;

use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IncluirCart extends Component
{
    use CartTrait;
    public $cartItems = [];
    public $userId;
    public $discount=0;
    public $total;
    public $subTotal;
    public $cashback=0;
    public $hasCashback = false;  // Estado inicial do switch


    protected $listeners = ['cartUpdated' => 'handleCartUpdated'];

    public function mount()
    {
        $this->userId();
        $this->handleCartUpdated();
    }

    public function handleCartUpdated()
    {
        $this->loadCartItemsTrait();
    }

    public function decrementQuantity(int $product_id)
    {
        $this->decreaseQuantityTrait($product_id);
    }

    public function incrementQuantity(int $product_id)
    {
        $this->increaseQuantityTrait($product_id);
    }

    public function removeFromCart($idItem){
        $this->removeFromCartTrait($idItem);
    }

    public function render()
    {
        return view('livewire.incluir-cart');
    }
}
