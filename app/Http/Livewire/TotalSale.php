<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;

class TotalSale extends Component
{
    use CartTrait;
    public $total = 0;
    public $cartItems = [];
    public $userId;
    public $discount =0;

    protected $listeners = ['totalSaleVendaUpdated' => 'handleTotalSaleVendaUpdated'];

    public function mount(){
        $this->userId = Auth::id();
        $this->loadCartItems();
    }

    /***
     *  //taxa do cliente caso a entrega seja motoby loja soma no total
     */
    public function handleTotalSaleVendaUpdated($formaEntrega){
       
        if ($formaEntrega == 'motoboy-loja') {
          $this->total += $this->cartItems[0]->clientes[0]->taxa ?? 0;
        }else{
            $this->total();
        }
    }

    public function render()
    {
        return view('livewire.total-sale');
    }
}
