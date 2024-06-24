<?php
namespace App\Traits;

use App\Http\Livewire\CartTotal;
use App\Http\Models\Carts;
use App\Http\Models\ProdutoVariacao;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
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

                $this->carts = new Carts();
                $this->carts->user_id = $this->userId;
                $this->carts->produto_variation_id = $product->id;
                $this->carts->name =  $product->subcodigo ." - ".$product['produtos'][0]->descricao ." - " . $product->variacao;
                $this->carts->price = $product->valor_varejo;
                $this->carts->quantidade = $cant;
                $this->carts->imagem = count($product->images) > 0 ? $product->images[0]->path : "";

                $this->carts->save();

               $this->loadCartItems();

                if (!$this->items->isEmpty()) {
                    if (!$this->items[0]->clientes->isEmpty()) {
                        $this->carts->cliente_id = $this->items[0]->clientes[0]->id;
                    }
                }

                //$this->total = $this->getTotalCartByUser(); //Cart::getTotal();
               // $this->totalPixDebitoCredito = $product->valor_varejo;
                //$this->itemsQuantity = Cart::getTotalQuantity();

                //crio na session o codigo venda
//                if(Session::get('codigoVenda') == null){
//                    $codigoVenda = $this->gerarNumeroAleatorioKN();
//                    Session::put('codigoVenda', $codigoVenda);
//                    //Emite o codigo venda para o input na tela
//                   // $this->emit('codigo-venda',$codigoVenda);
//                }

                $totais = $this->getTotalCartTraitByUser();

                $this->total = $totais['total'];
                $this->subTotal = $totais['subTotal'];
                $this->taxa = $totais['taxa'];



                $this->emit('scan-ok','Produto Adicionado!');

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
        $exist = Carts::where('produto_variation_id', $productId)->where('user_id',$this->userId)->first();

        if($exist)
                return true;
        else
                return false;
    }

    /**
     * Incrmenta a quantidade de produto no componente CART
     * @param $product_id
     * @param int $cant
     */
    public function IncreaseQuantity($product_id, $cant = 1)
    {
            $product = Carts::with('variations')->where('produto_variation_id', $product_id)
                    ->where('user_id',$this->userId)
                    ->where('status', 'ABERTO')
                    ->first();

            if($product)
            {
                if($product->quantidade > ($cant + $product->variations[0]->quantidade))
                {
                    $this->emit('no-stock','Atenção! Estoque insuficiente');
                    return;
                }

                if($product->quantidade+1 >= 5){
                    $product->price = $product->variations[0]->valor_atacado_10un;
                }else{
                    $product->price = $product->variations[0]->valor_varejo;
                }
                $product->quantidade++;

                if($product->update()){
                    $this->emit('scan-ok', "Produto atualizado com sucesso!");
                    $this->emit('atualizarCarrinho');
                }else{
                    $this->emit('global-error', "Não foi possivel atualizar o produto!");
                }
            }
    }

    /**
     * Passsa para o componente CART a solicitação para remover o item
     * @param $productId
     */
    public function removeCartItem($productId)
    {
        $delete = Carts::find($productId)->delete();

        if($delete){
//            $totais = $this->getTotalCartTraitByUser();
//            $this->total = $totais['total'];
//            $this->subTotal = $totais['subTotal'];
//            $this->taxa = $totais['taxa'];
//
//            $this->clienteName = $this->getClientCartTrait();
//
//            $this->items = $this->loadCartItems();

            $this->emit('scan-remove', 'Produto removido!');
            $this->emit('refresh', true);

        }else{
            $this->emit('global-error', "Não foi possivel remover o produto!");
        }
    }

    /**
     * Decrementa a quantidade de produto no componente CART
     * @param $product_id
     * @param int $cant
     */
    public function decreaseQuantity($product_id, $cant = 1){

        $product = Carts::with('variations')->where('produto_variation_id', $product_id)
            ->where('user_id',$this->userId)
            ->where('status', 'ABERTO')
            ->first();

        if($product) {
            $product->price = $product->variations[0]->valor_atacado_10un;

            if ((($product->quantity) - 1) < 5) {
                $product->price = $product->variations[0]->valor_varejo;
            }

            $product->quantidade--;

            if($product->update()){
                $this->emit('scan-ok', "Produto atualizado com sucesso!");
                $this->emit('atualizarCarrinho');
            }else{
                $this->emit('global-error', "Não foi possivel atualizar o produto!");
            }

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

//    public function updateCartTrait($valorTotalPixDebitoCredito,$valorTotalCredito,$valorCaixaFechad){
//      //     dd("updateCartTrait");
//        Cart::session($this->userId)->getContent();
//
//        $this->total = Cart::getTotal();
//        $this->itemsQuantity = Cart::getTotalQuantity();
//        $this->totalPixDebitoCredito =  $valorTotalPixDebitoCredito;
//        $this->totalCredito =  $valorTotalCredito;
//
//        $this->emitTo('cart-component','updateCartAtacado', null);
//    }

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


    /***
     * Retorna os totais e o frete
    */
    public function getTotalCartTraitByUser()
    {
        // Obtendo todos os itens do carrinho do usuário
        $items = Carts::with('clientes')->where('user_id', Auth::id())->where('status' , 'ABERTO')->get();

        // Calculando o total
        $subTotal = $items->sum(function ($item) {
            return $item->quantidade * $item->price;
        });

        $taxa = $items[0]->clientes[0]->taxa ?? null;

        if ($taxa !== null) {
            $taxa = $items[0]->clientes[0]->taxa;
        } else {
            $taxa = 0;
        }

        $total = $subTotal + $taxa;

        return  array("total" => $total, "subTotal" => $subTotal, "taxa" => $taxa);
    }

    public function getClientItemCartTrait(){
        return   $this->cartItems = Carts::with(['variations','clientes'])
            ->where('user_id', Auth::id())
            ->where('status' , 'ABERTO')->get();

     //   return !empty($carts['clientes']) && count($carts['clientes']) > 0 ? $carts['clientes'][0]->nome : null;
      // return $carts ?: ['clientes' => []];
    }


    public function loadCartItems()
    {
        $this->items = Carts::with(['variations','clientes'])
            ->where('user_id',  $this->userId )
            ->where('status',  'ABERTO' )
            ->get();

       // dd($this->items);
        $totais = $this->getTotalCartTraitByUser();
        $this->total = $totais['total'];
        $this->subTotal = $totais['subTotal'];
        $this->taxa = $totais['taxa'];

        $this->clienteName = $this->getClientCartTrait();
    }

}
