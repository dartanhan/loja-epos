<?php

namespace App\Http\Livewire;

use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IncluirTotais extends Component
{
    use CartTrait;
    public $cartItems = [];
    public $userId;
    public $discount;
    public $total;
    public $subTotal;
    public $cashback=0;
    public $hasCashback = false;  // Estado inicial do switch

    protected $listeners = ['totaisUpdated' => 'handleTotaisUpdated'];

    public function mount()
    {
        $this->userId();
        $this->handleTotaisUpdated();
    }

    public function handleTotaisUpdated()
    {
        $this->loadCartItemsTrait();
    }

    public function render()
    {
        return view('livewire.incluir-totais');
    }
}
