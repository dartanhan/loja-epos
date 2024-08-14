<?php

namespace App\Http\Livewire;

use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IncluirCliente extends Component
{
    use CartTrait;
    public $cartItems = [];
    public $userId;
    public $discount;
    public $cashback=0;
    public $hasCashback = false;  // Estado inicial do switch

    protected $listeners = ['atualizarCliente' => 'mount'];

    public function mount()
    {
        $this->userId = $this->userId();
        $this->loadCartItemsTrait();
    }

    public function render()
    {
        return view('livewire.incluir-cliente');
    }
}
