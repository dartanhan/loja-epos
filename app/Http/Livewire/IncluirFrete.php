<?php

namespace App\Http\Livewire;

use App\Traits\CartTrait;
use Livewire\Component;

class IncluirFrete extends Component
{
//    use CartTrait;
//    public $cartItems = [];
//    public $showFrete = false;
//    public $frete = 0;
//    public $userId;
//    public $discount;
//
//    protected $listeners = ['freteVendaUpdated' => 'handleFreteVendaUpdated'];
//
//    public function mount()
//    {
//        $this->loadCartItems();
//    }
//
//    public function handleFreteVendaUpdated($tipoVenda)
//    {
//        $this->frete = 0;
//        if($tipoVenda == 'motoboy-loja'){
//            if ($this->cartItems->isNotEmpty() && optional($this->cartItems->first()->clientes)->isNotEmpty()){
//                $this->frete = $this->cartItems[0]->clientes[0]->taxa;
//            }
//        }
//
//    }
//
//    public function render()
//    {
//        return view('livewire.incluir-frete');
//    }
}
