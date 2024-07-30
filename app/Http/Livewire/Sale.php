<?php

namespace App\Http\Livewire;

use App\Http\Models\FormaPagamento;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sale extends Component
{
    use CartTrait;
    public $userId;
    public $cartItems;
    public $total;
    public $paymentMethods = [];
    public $selectedItemFormaPgto = false;
    public $codeSale;
    public $tipo_venda_id;
    public $troco=0;
    public $valorRecebido;
    public $dinheiro;
    public $discount;
    public $cashback=0;
    public $frete=0;


    protected $listeners = ['vendaUpdated' => 'handleVendaUpdated',
        'tipoVenda'=>'tipoVenda','storeSale'=>'storeSale','updatedValorRecebido' => 'updatedValorRecebido'];

    public function mount($code = null)
    {
        $this->userId = Auth::id();
        $this->loadCartItems();
        $this->loadPaymentMethods();
        $this->codeSale = $code;
    }

    public function loadPaymentMethods()
    {
        // Supondo que você tenha uma tabela 'payment_methods' no banco de dados
        $this->paymentMethods = FormaPagamento::where('status', true)->orderBy('nome','asc')->get();
    }

    /**
     * Pego o código do Tipo da Venda no componente Sale
     * @param $tipoVendaId
     */
    public function tipoVenda($tipoVendaId){
        $this->tipo_venda_id = $tipoVendaId;
    }

    /**
     * Ativa ou desativa o botão de finalizar venda
     * Chama a função no javascript
     * @param $value
     */
    public function updatedSelectedItemFormaPgto($value)
    {
        $this->emit('formaPgtoChanged', $value);
    }

    /**
     * Altera o valor total caso tenha selecionado na entrega o motoboy da loja, pega taxa fixa de entrega
     * @param $typeSale
     */
    public function handleVendaUpdated($typeSale){
        //dd($typeSale);
        $this->loadCartItems();
        $this->frete = 0;
        if($typeSale == 'motoboy-loja'){
            if ($this->cartItems->isNotEmpty() && optional($this->cartItems->first()->clientes)->isNotEmpty()){
                $this->total += $this->cartItems[0]->clientes[0]->taxa;
                $this->frete = $this->cartItems[0]->clientes[0]->taxa;
               // $this->emitTo('cart-component','atualizarCarrinho');
            }
        }
    }

    public function storeSale($data)
    {
        $this->storeSaleTrait($data);
    }

    /**
     * Calcula o troco ao digitar no campo dinheiro
     * @param $value
     */

    public function updatedValorRecebido($value)
    {
        // Remover caracteres não numéricos e converter para float
        $value = preg_replace('/[^\d,]/', '', $value);
        $value = str_replace(',', '.', $value);
        $this->dinheiro = floatval($value);


        $this->troco = $this->dinheiro > 0 ? $this->dinheiro - $this->total : 0;
        if($this->troco < 0){
            $this->emit('btn-finalizar-venda',false, $this->dinheiro);
        }else{
            $this->emit('btn-finalizar-venda',true, $this->dinheiro);
        }
    }

    /**
     * Renderiza a página
    */
    public function render()
    {
        return view('livewire.sale');
    }
}
