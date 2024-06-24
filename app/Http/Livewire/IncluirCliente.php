<?php

namespace App\Http\Livewire;

use App\Traits\CartTrait;
use Livewire\Component;

class IncluirCliente extends Component
{
//    use CartTrait;
//
//    public $carts;
//
//    protected $listeners = ['atualizarCliente' => 'atualizarCarts'];
//
//    public function mount()
//    {
//        $this->atualizarCarts();
//        $totais = $this->getTotalCartTraitByUser();
//        if($totais['total']){
//            $this->emit('cliente-ok',false);
//        }
//    }
//
//    public function atualizarCarts()
//    {
//        $this->carts = $this->getClientCartTrait();
//    }
//
//    public function getClienteName()
//    {
//        return !empty($this->carts['clientes']) && count($this->carts['clientes']) > 0 ? $this->carts['clientes'][0]->nome : null;
//    }
//
//    public function render()
//    {
//        return view('livewire.incluir-cliente', [
//            'clienteName' => $this->getClienteName(),
//        ]);
//    }
}
