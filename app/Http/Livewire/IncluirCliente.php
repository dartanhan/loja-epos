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

    protected $listeners = ['atualizarCliente' => 'mount'];

    public function mount()
    {
        $this->userId = Auth::id();
        $this->loadCartItems();
    }

    public function render()
    {
        return view('livewire.incluir-cliente');
    }
}
