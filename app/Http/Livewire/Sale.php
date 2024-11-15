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
    public $cartItems=[];
    public $total;
    public $paymentMethods = [];
    public $selectedItemFormaPgto = false;
    public $codeSale;
    public $tipo_venda_id;
    public $troco=0;
    public $dinheiro;
    public $discount;
    public $cashback=0;
    public $frete=0;
    public $formaId;
    public $css;
    public $hasCashback = false;

    protected $listeners = ['vendaUpdated' => 'handleVendaUpdated','loadSales' => 'mount',
        'tipoVenda'=>'tipoVenda','storeSale'=>'storeSale','updatedValorRecebido' => 'updatedValorRecebido'];

    public function mount()
    {
        $this->userId();
        $this->loadCartItemsTrait();
        $this->loadPaymentMethods();
        //$this->codeSale = $code;
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
//    public function updatedSelectedItemFormaPgto($value)
//    {
//        $this->emit('formaPgtoChanged', $value);
//
//    }

    /**
     * Altera o valor total caso tenha selecionado na entrega o motoboy da loja, pega taxa fixa de entrega
     * @param $typeSale
     * @param $formaId
     */
    public function handleVendaUpdated($typeSale,$formaId=null){
       // dd([$typeSale,$formaId]);
       // $this->loadCartItemsTrait();
        $this->formaId = $formaId;
        $this->frete = 0;
        if($typeSale == 'motoboy-loja'){
            if ($this->cartItems->isNotEmpty() && optional($this->cartItems->first()->clientes)->isNotEmpty()){
                $this->total += $this->cartItems[0]->clientes[0]->taxa;
                $this->frete = $this->cartItems[0]->clientes[0]->taxa;
            }
        }else {
            $this->total();
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
            $this->emit('btn-finalizar-venda',false,  $this->troco);
            $this->css = 'text-red';
        }else{
            $this->emit('btn-finalizar-venda',true,  $this->troco);
            $this->css = '';
        }

    }

    /**
     * Ativa o uso do cashback ou desativa
     */
    public function toggleCashback()
    {
        $this->hasCashback = !$this->hasCashback;

        // Adicione a lógica para aplicar o cashback
        $this->total();

        // Atualize a venda ou faça qualquer outra ação necessária
        $this->emitTo('total-sale','totalSaleVendaUpdated','',$this->hasCashback);

    }

    /**
     * Renderiza a página
    */
    public function render()
    {
        return view('livewire.sale');
    }
}
