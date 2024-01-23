<?php
namespace App\Http\Livewire;

use App\Traits\CartTrait;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartTotal extends Component
{
    use CartTrait;
    public $total, $itemsQuantity,$userId,$totalPixDebitoCredito, $totalCredito;

    public function mount(){
        $this->userId = Auth::id();
    }

    public function render()
    {

        Cart::session($this->userId)->getContent();
        $this->total = Cart::getTotal();
        //$this->itemsQuantity = Cart::getTotalQuantity();
        $this->updateCart();
        return view('livewire.cartTotal');
    }

    protected $listeners = [
        'updateCart' => 'updateCart',
        'trashCartTotal'=>'trashCartTotal'
    ];

    /**
     * Atualiza a parte de totais do carrinho
     * "valor_varejo" => "22.00"
        "valor_atacado" => "20.00"
        "valor_atacado_5un" => "20.00"
        "valor_atacado_10un" => "20.00" -- caixa fechada
        "valor_lista" => "20.00" -- dinheiro
     * "valor_parcelado" => 35.00 - CRÉDITO(3X À 6X)
     * "valor_cartao_pix" => 25.00 - -PIX, DÉBITO E CRÉDITO(2X)
     *
     * array:1 [▼
        100310 => 22.0
    ]
     */
    function updateCart(){
           //dd(Cart::session($this->userId)->getContent());
        if(Cart::session($this->userId)->getContent()){

            $produtos = [];
            foreach (Cart::session($this->userId)->getContent() as $key => $item){
                // dd($item);

                /**
                 * Regra do atacado
                */
                $price = $item->associatedModel->valor_varejo;
                if(count(Cart::session($this->userId)->getContent()) >=10){
                    $price = $item->associatedModel->valor_atacado;
                }

                $produtos[$key] =
                    ["subcodigo" =>$item->associatedModel->subcodigo,
                        'valor' => $price,
                        'quantity' =>  $item->quantity,
                        'valorTotalPixDebitoCredito' => $item->associatedModel->valor_cartao_pix * $item->quantity,
                        'valorTotalCredito' => $item->associatedModel->valor_parcelado * $item->quantity,
                        'caixaFechada' =>  $item->associatedModel->valor_atacado_10un * $item->quantity
                    ];
            }
            $valorTotalPixDebitoCredito = array_column($produtos, 'valorTotalPixDebitoCredito');
            $valorTotalCredito = array_column($produtos, 'valorTotalCredito');
            $valorCaixaFechada = array_column($produtos, 'caixaFechada');

            $valorTotalPixDebitoCredito = array_sum($valorTotalPixDebitoCredito);
            $valorTotalCredito = array_sum($valorTotalCredito);
            $valorCaixaFechada = array_sum($valorCaixaFechada);


            $this->updateCartTrait($valorTotalPixDebitoCredito,$valorTotalCredito,$valorCaixaFechada);

        }
    }

    /***
    * Limpa os totais da venda
     */
    function trashCartTotal(){
        Cart::session($this->userId)->getContent();
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->totalPixDebitoCredito =  0;
        $this->totalCredito =  0;
    }
}
