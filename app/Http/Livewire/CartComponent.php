<?php
// CartComponent.php

namespace App\Http\Livewire;

use App\Http\Models\VendaProdutos;
use App\Http\Models\VendasProdutos;
use App\Http\Models\Vendas;
use App\Http\Models\ProdutoVariacao;
use App\Traits\CartTrait;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class CartComponent extends Component {
    use CartTrait;
    public $cartItems = [];
    public $itemsQuantity, $change,$userId,$barcode,$total,$vendas,$vendasProdutos;
    public $number = 0;
    protected $listeners = ['addToCart' => 'addToCart',
                            'removeItem' => 'removeItem',
                            'updateCart'=>'updateCart',
                            'createSale'   => 'createSale',
                            'saveSale' => 'saveSale',
                            'updateCartAtacado' => 'updateCartAtacado'
    ];


    /**
     * Monta o ID do user para o compomente do carrinho CART
     * @param Vendas $vendas
     * @param VendaProdutos $vendasProdutos
     */
    public function mount(Vendas $vendas, VendaProdutos $vendasProdutos){
        $this->userId = Auth::id();

        $this->vendas = $vendas;
        $this->vendasProdutos = $vendasProdutos;

        $this->emit('codigo-venda', Session::get('codigoVenda'));
    }

    /**
     *Busaca o prduto para venda no Carrinho
     * @param $barcode
     */
    public function addToCart($barcode){
      //  dd($barcode);
        $this->ScanearCode($barcode);
    }

    /**
     * Exibe o compoenente CAR na tela
     */
    public function render(){
        Cart::session($this->userId);

        return view('livewire.cart');
    }

    /**
     * Atualiza a quantidade de item no carrinho
     * @param $productId
     */
    public function updateQty(ProdutoVariacao $product, $cant = 1, $qtdCart)
    {
       // dd($product->id , $cant, $qtdCart);

        if ($cant > $qtdCart)
           // $this->removeItem($product->id);
            $this->IncreaseQuantity($product,$cant);
        else
           // $this->UpdateQuantity($product, $cant);
            $this->decreaseQuantity($product, $cant);

        $this->emit('pinta-linha-atacado', null);
    }

    /**
     * Remove o item no carrinho
     * @param $productId
     */
    public function removeItem($productId)
    {
       // dd(Cart::session($this->userId)->getContent());
        Cart::session($this->userId)->remove($productId);
        Cart::session($this->userId)->getContent();

        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();

        $this->emit('scan-remove', 'Produto removido!');
        $this->emitTo('cart-total','updateCart', null);
    }

    /**
    * Função que realiza fechamento da venda
     */
    public function createSale()
    {
        $data = "";
            // Dados do Carrinho que deseja enviar para o frontend
        $data = Cart::session($this->userId)->getContent();

        $cart = [];
        $totalPDC = 0;
        $totalCRED = 0;

        foreach ($data as $key => $value){
            //dinheiro
            $total = number_format($value->quantity * $value->price, 2, '.', ',');
            //pix,debito,crédito
            $totalPixDebitoCredito = number_format($value->quantity * $value->associatedModel->valor_cartao_pix, 2, '.', ',');
            //Crédito
            $totalCredito = number_format($value->quantity * $value->associatedModel->valor_parcelado, 2, '.', ',');

            //Soma os totais
            $totalPDC += $totalPixDebitoCredito;
            $totalCRED += $totalCredito;

            //cria o array de devolução apra mandar para a API
            $cart[$key] = [
                "total" => $total,
                "totalPDC" => $totalPixDebitoCredito,
                "totalCredito" => $totalCredito,
                "variacao_id" =>  $value->id,
                "quantity" =>  $value->quantity,
                "subcodigo"=> (int)$value->associatedModel->subcodigo
            ];
            $cart['totalGeralDinner'] = number_format(Cart::getTotal(), 2, '.', ',');
            $cart['totalGeralPDC'] = number_format($totalPDC, 2, '.', ',');
            $cart['totalGeralCredito'] = number_format($totalCRED, 2, '.', ',');
            $cart['totalQuantity'] = Cart::getTotalQuantity();
        }
        // Emitir evento Livewire com os dados para o JS
        $this->emit('informacoesAtualizadas', $cart);

    }

    /**
     * Salva a venda
     * @param $codeSale
     * @param $totalVenda
     * @param $tipoVenda
     * @param array $dataArray
     */
    public function saveSale($codeSale,$totalVenda, $tipoVenda){
        $data = Cart::session($this->userId)->getContent();
      // dd($data,$tipoVenda);
        $total = 0;
        $price =0;

        //$codeSale = "KN".rand(0,999999);
        //cria a venda
        $sale = $this->vendas->create(["codigo_venda" =>  $codeSale,//$dados["codigo_venda"],
            "loja_id" =>  1,//$dados["loja_id"],
            "valor_total" =>  $totalVenda,//$dados["valor_total"],
            "usuario_id" =>  $this->userId,//isset($dados["usuario_id"]) ? $dados["usuario_id"] : 3,
            "cliente_id" =>  $this->userId,//$dados["clienteModel"]["id"] !== 0 ? $dados["clienteModel"]["id"] : null,
            "tipo_venda_id" => $this->userId]); //$dados["tipoEntregaCliente"]]);

        //Salva itens da venda
        if ($sale->exists) {
            foreach ($data as $key => $value){
                switch ($tipoVenda) {
                    case 'F1':
                        //$total = $value->quantity * $value->price;
                        $price = $value->price;
                        break;
                    case 'F2':
                        $price = $value->associatedModel->valor_cartao_pix;
                        break;
                    case 'F3':
                        $price = $value->associatedModel->valor_parcelado;
                        break;
                }

                $this->vendasProdutos = new VendaProdutos();
                $this->vendasProdutos->venda_id = $sale->id;
                $this->vendasProdutos->codigo_produto = $value->associatedModel->subcodigo;//$dados["produtos"][$i]["codigo_produto"];
                $this->vendasProdutos->descricao = $value->associatedModel->produtos[0]->descricao ." - ". $value->associatedModel->variacao;//$dados["produtos"][$i]["descricao"];
                $this->vendasProdutos->valor_produto = $price;//$dados["produtos"][$i]["valor_produto"];
                $this->vendasProdutos->quantidade = $value->quantity;//$dados["produtos"][$i]["quantidade"];
                $this->vendasProdutos->troca = 0; //$dados["produtos"][$i]["troca"];
                $this->vendasProdutos->fornecedor_id = $value->associatedModel->fornecedor; //$dados["produtos"][$i]["fornecedor_id"];
                $this->vendasProdutos->categoria_id = $value->associatedModel->produtos[0]->categoria_id;//$dados["produtos"][$i]["categoria_id"];

                $saveSale = $this->vendasProdutos->save();

                //realiza a baixa do produto
                if($saveSale){
                 //   $id = $dados["produtos"][$i]["id"]; // id do produto
                 //   $sub_codigo = $dados["produtos"][$i]["codigo_produto"]; // id do produto


                }

            }
            $this->trashCart();
        }

    }

    /**
    *
     */
    function updateCartAtacado(){
       // dd("updateCartAtacado");

        foreach (Cart::session($this->userId)->getContent() as $key => $item) {
            // dd($item->id);
            //$product = Cart::get($item->id);

            /**
             * Regra do atacado
             */
            $price = $item->associatedModel->valor_varejo;
            if(count(Cart::session($this->userId)->getContent()) >=10){
                $price = $item->associatedModel->valor_atacado;
            }
            $products = array(
                'id' => $item->id,
                'name' => $item->name,
                'price' => $price,
                'quantity' => $item->quantity,
                'attributes' => [],
                'associatedModel' => $item->associatedModel
            );
            Cart::remove($item->id);
            Cart::session($this->userId)->add($products);
            $this->emit('pinta-linha-atacado', null);
        }

    }
}
