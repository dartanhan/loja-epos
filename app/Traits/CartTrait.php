<?php
namespace App\Traits;

use App\Http\Livewire\CartTotal;
use App\Http\Models\ProdutoVariacao;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Session;

trait CartTrait {

    /**
     * Busca na base pelo cádigo do produto e adiciona no componente CART
     * @param $barcode
     * @param int $cant
     */
    public function ScanearCode($barcode, $cant = 1)
    {

        $product = ProdutoVariacao::with('images','produtos')
            ->where("subcodigo",$barcode)
            ->where("status",1)
            ->first();
       // dd($product);
        if($product == null || empty($product))
        {
                $this->emit('scan-notfound','Produto não registrado',1);
        }  else {

                if($this->InCart($product->id))
                {
                        $this->IncreaseQuantity($product);
                        return;
                }

                if($product->quantidade <1)
                {
                       $this->emit('no-stock','Atenção! Estoque insuficiente *');
                        return;
                }
          //  dd(count(Cart::session($this->userId)->getContent()));
                Cart::session($this->userId)->add(array(
                    'id' => $product->id,
                    'name' => $product->subcodigo ." - ".$product['produtos'][0]->descricao ." - " . $product->variacao,
                    'price' => $product->valor_varejo, //dinheiro
                    'quantity' => $cant,
                    'attributes' => [],
                    'associatedModel' => $product
                ));

              // dd(count(Cart::session($this->userId)->getContent()));

                $this->total = Cart::getTotal();
                $this->totalPixDebitoCredito = $product->valor_varejo;
                $this->itemsQuantity = Cart::getTotalQuantity();

                //crio na session o codigo venda
                if(Session::get('codigoVenda') == null){
                    $codigoVenda = $this->gerarNumeroAleatorioKN();
                    Session::put('codigoVenda', $codigoVenda);
                    //Emite o codigo venda para o input na tela
                    $this->emit('codigo-venda',$codigoVenda);
                }

                $this->emit('scan-ok','Produto Adicionado!');
                $this->emitTo('cart-total','updateCart', $product->valor_varejo);
        }
    }

    /**
    * Gera o nímero do código da venda
     */
    function gerarNumeroAleatorioKN() {
        // Gera um número aleatório de 0 a 99999
        $numeroAleatorio = rand(0, 99999);

        // Formata o número para ter exatamente 5 dígitos (adicionando zeros à esquerda se necessário)
        $numeroFormatado = sprintf("%05d", $numeroAleatorio);

        // Concatena o número formatado com "KN"
        return "KN".$numeroFormatado;
    }

    /**
     * Verifica se já existe no carrinho o produto no componente CART
     * @param $productId
     * @return bool
     */
    public function InCart($productId)
    {
        Cart::session($this->userId)->getContent();
        $exist = Cart::get($productId);

        if($exist)
                return true;
        else
                return false;
    }

    /**
     * Incrmenta a quantidade de produto no componente CART
     * @param $product
     * @param int $cant
     */
    public function IncreaseQuantity($product, $cant = 1)
    {
            $msg ='';
            Cart::session($this->userId)->getContent();
            $exist = Cart::get($product->products_id);
            if($exist)
                    $msg = 'Quantidade Atualizada';
            else
                    $msg ='Produto Adicionado';

            if($exist)
            {
                    if($product->quantidade < ($cant + $exist->quantity))
                    {
                            $this->emit('no-stock','Atenção! Estoque insuficiente');
                            return;
                    }
            }

           // dd(Cart::get($product->id)->quantity);

            if(Cart::get($product->id)->quantity+1 >= 5){
                $price = $product->valor_atacado;
                $this->emit('color-tr',['id' => $product->id, 'color' => "#ffcccc"] );
            }else{
                $price = $product->valor_varejo;
            }

            Cart::session($this->userId)->add(array(
                'id' => $product->id,
                'name' => $product->subcodigo ."-".$product['produtos'][0]->descricao ."-" . $product->variacao,
                'price' => $price,
                'quantity' => 1,
                'attributes' => ['subcodigo' => $product->subcodigo],
                'associatedModel' => $product
            ));

            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();

            $this->emit('scan-ok', $msg);
            $this->emitTo('cart-total','updateCart', $this->total);

    }

    /**
     * Passsa para o componente CART a solicitação para remover o item
     * @param $productId
     */
    public function removeItem($productId)
    {
         $this->emitTo('cart-component', 'removeItem', $productId);
         $this->emitTo('cart-total','updateCart', $this->total);
    }

    /**
     * Decrementa a quantidade de produto no componente CART
     * @param $product
     * @param int $cant
     */
    public function decreaseQuantity($product, $cant = 1){

         // or any string represents user identifier

            Cart::session($this->userId)->getContent();

            $item = Cart::get($product->id);

            Cart::remove($product->id);

            // si el producto no tiene imagen, mostramos una default
           // $img = (count($item->attributes) > 0 ? $item->attributes[0] : ProdutoVariacao::find($productId)->imagem);

            $newQty = ($item->quantity) - 1;

            $price = $product->valor_atacado;

            $this->emit('color-tr',['id' => $product->id, 'color' => "#ffcccc"] );
            if($newQty <= 4){
                $price = $product->valor_varejo;
                $this->emit('color-tr',['id' => $product->id, 'color' => "white"] );
            }


            if($newQty > 0) {
                //Cart::add($item->id, $item->name, $item->price, $newQty, null);
                Cart::session($this->userId)->add(array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $price,
                    'quantity' => $newQty,
                    'attributes' => [],
                    'associatedModel' => $product
                ));

                $this->total = Cart::getTotal();
                $this->itemsQuantity = Cart::getTotalQuantity();
                $this->emit('scan-ok', 'Quantidade atualizada');
                $this->emitTo('cart-total', 'updateCart', $this->total);
            }else{
                $this->emit('scan-ok', 'Quantidade atualizada');
                return;
            }
    }

    /**
     * Limpa o carrinho da venda
    */
    public function trashCart() {
            Cart::session($this->userId)->getContent();
            Cart::clear();
            $this->efectivo =0;
            $this->change =0;
            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();
            $this->totalPixDebitoCredito =  0;
            $this->totalCredito =  0;

            Session::remove("codigoVenda");
            $this->emit('codigo-venda',null);
            $this->emit('global-msg', 'Venda Finalizada com Sucesso!');
            $this->emitTo('cart-total','trashCartTotal', null);
    }

    public function updateCartTrait($valorTotalPixDebitoCredito,$valorTotalCredito,$valorCaixaFechad){
      //     dd("updateCartTrait");
        Cart::session($this->userId)->getContent();

        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->totalPixDebitoCredito =  $valorTotalPixDebitoCredito;
        $this->totalCredito =  $valorTotalCredito;

        $this->emitTo('cart-component','updateCartAtacado', null);

    }

//public function updateQuantity($product, $cant = 1)
//{
//        $title='';
//
//        $userId = auth()->user()->id; // or any string represents user identifier
//        Cart::session($userId)->getContent();
//        $exist = Cart::get($product->id);
//
//        //quantidade de produtros vinda no relacionamento
//        $quantidade = $exist->associatedModel->quantidade;
//
//        if($exist)
//                $title = 'Quantidade atualizada!';
//        else
//                $title ='Produto adicionado!';
//
//        if($exist)
//        {
//                if($quantidade < $cant)
//                {
//                        $this->emit('no-stock','Atenção! Estoque insuficiente.');
//                        return;
//                }
//        }
//
//        $this->removeItem($product->id);
//
//        if($cant > 0)
//        {
//            Cart::add($product->products_id, $product->variacao, $product->valor_varejo, $cant, null);
//                //Cart::add($product->id, $product->name, $product->price, $cant, $product->image);
//
//                $this->total = Cart::getTotal();
//                $this->itemsQuantity = Cart::getTotalQuantity();
//
//                $this->emit('scan-ok', $title);
//
//        }
//}
}
