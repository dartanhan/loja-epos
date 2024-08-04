<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;

class CardDinheiro extends Component
{
    use CartTrait;
    public $total = 0;
    public $cartItems = [];
    public $userId;
    public $discount =0;
    public $subTotal=0;



    public function mount(){

    }


    public function render()
    {
        return view('livewire.card-dinheiro');
    }
}
