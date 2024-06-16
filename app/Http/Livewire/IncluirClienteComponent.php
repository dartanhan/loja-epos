<?php

namespace App\Http\Livewire;


use App\Http\Models\Carts;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IncluirClienteComponent extends Component
{
    use CartTrait;

    protected $listeners = ['clienteAtualizado' => 'loadCartItems'];

    public function mount(){
        $this->userId = Auth::id();
        $this->loadCartItems();
    }

    public function render()
    {
    //    $this->loadCartItems();

        return view('livewire.incluir-cliente');
    }
}
