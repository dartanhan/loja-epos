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
    public $subTotal=0;
    public $cashback=0;
    public $hasCashback =false;  // Estado inicial do switch

    protected $listeners = ['totalSaleVendaUpdated' => 'handleTotalSaleVendaUpdated'];

    public function mount(){
        $this->userId();
        $this->loadCartItemsTrait();
    }

    /***
     *  //taxa do cliente caso a entrega seja motoby loja soma no total
     * @param $formaEntrega
     * @param $hasCashback
     */
    public function handleTotalSaleVendaUpdated($formaEntrega,$hasCashback){
        $this->loadCartItemsTrait();

        //atualiza a variável do $hasCashback
        $this->hasCashback = $hasCashback;

        //verifica se é motoboy da loja
        if ($formaEntrega == 'motoboy-loja') {
            $this->total += $this->cartItems[0]->clientes[0]->taxa ?? 0;
        }else{
            $this->total();//-$this->discount();
            $this->subTotal();
            //$this->subTotal = $this->subTotal();
        }
    }

    public function render()
    {
        return view('livewire.total-sale');
    }
}
