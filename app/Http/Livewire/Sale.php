<?php

namespace App\Http\Livewire;

use App\Traits\CartTrait;
use Livewire\Component;

class Sale extends Component
{
    use CartTrait;
    public $cartItens;
    public $cartTotal;

    public function mount()
    {
      $this->cartTotal = $this->getTotalCartTraitByUser();
    }

    public function render()
    {
        return view('livewire.sale');
    }
}
