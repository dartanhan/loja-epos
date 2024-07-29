<?php
namespace App\Traits;

use App\Constants\IconConstants;
use App\Http\Livewire\CartTotal;
use App\Http\Models\Carts;
use App\Http\Models\ProdutoVariacao;
use App\Http\Models\TaxaCartao;
use App\Http\Models\VendaProdutos;
use App\Http\Models\Vendas;
use App\Http\Models\VendasProdutosTipoPagamento;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\True_;

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
                $this->emit("message", "Atenção! Produto não registrado!", IconConstants::ICON_SCAN,IconConstants::COLOR_RED);
        }  else {

                if($this->InCart($product->id))
                {
                        $this->IncreaseQuantity($product);
                        return;
                }

                if($product->quantidade <1)
                {
                    $this->emit("message", "Atenção! Estoque insuficiente!", IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
                    return;
                }

                $this->carts = new Carts();
                $this->carts->user_id = $this->userId;
                $this->carts->produto_variation_id = $product->id;
                $this->carts->name =  $product['produtos'][0]->descricao ." - " . $product->variacao;
                $this->carts->codigo_produto = $product->subcodigo;
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
                $totais = $this->getTotalCartTraitByUser();

                $this->total = $totais['total'];
                $this->subTotal = $totais['subTotal'];
                $this->taxa = $totais['taxa'];

                $this->emit("message", "Produto Adicionado!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,false,true);
        }
    }

    /**
    * Gera o nímero do código da venda
     */
    function getCodeSaleKN() {
        // Gera um número aleatório de 0 a 99999
        $numeroAleatorio = rand(0, 99999);

        // Concatena o número formatado com "KN"
        return "KN". sprintf("%05d", $numeroAleatorio);;
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
                    $this->emit("message", "Atenção! Estoque insuficiente!", IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
                    return;
                }

                if($product->quantidade+1 >= 5){
                    $product->price = $product->variations[0]->valor_atacado_10un;
                }else{
                    $product->price = $product->variations[0]->valor_varejo;
                }
                $product->quantidade++;

                if($product->update()){
                  //  $this->emit('scan-ok', "Produto atualizado com sucesso!");
                    $this->emit("message", "Produto atualizado com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
                    $this->emit('atualizarCarrinho');
                }else{
                    $this->emit('global-error', "Não foi possivel atualizar o produto!");
                }
            }
        $this->total = $this->getTotalCartTraitByUser()['total'];
    }

    /**
     * Passsa para o componente CART a solicitação para remover o item
     * @param $productId
     */
    public function removeCartItem($productId)
    {
        $delete = Carts::find($productId)->delete();

        if($delete){
            //$this->emit('scan-remove', 'Produto removido!');
            $this->emit("message", "Produto removido!", IconConstants::ICON_REMOVE_CART,IconConstants::COLOR_RED);
            $this->emit('refresh', true);
        }else{
            $this->emit("message", "Não foi possivel remover o produto!", IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
        }
        $this->total = $this->getTotalCartTraitByUser()['total'];
        $this->discount($this->cartItems);
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
                //$this->emit('scan-ok', "Produto atualizado com sucesso!");
                $this->emit("message", "Produto atualizado com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
                $this->emit('atualizarCarrinho');
            }else{
                $this->emit('global-error', "Não foi possivel atualizar o produto!");
                $this->emit("message", "Não foi possivel atualizar o produto!", IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
            }
            $this->total = $this->getTotalCartTraitByUser()['total'];
            $this->discount($this->cartItems);
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

    /**
     * Remove o cliente da venda
     * @param int $user_id
     * @param int $cliente_id
     */
    public function removeClientCartTrait(int $user_id, int $cliente_id){

        $items = Carts::
                    where('user_id', $user_id)
                    ->where('cliente_id', $cliente_id)
                    ->where('status' , 'ABERTO')
                    ->get();

        foreach ($items as $item){
            $item->cliente_id = null;
            $item->save();
        }
       // $this->emit('global-msg', 'Cliente removido da venda, com sucesso!!');
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


    /***
     * Retorna os totais e o frete
     * @param $items
     * @return array
     */
//    public function getTotalCartTraitByUser($items)
//    {
//        // Obtendo todos os itens do carrinho do usuário
//       // $items = Carts::with('clientes')->where('user_id', Auth::id())->where('status' , 'ABERTO')->get();
//
//        // Calculando o total
//        $subTotal = $items->sum(function ($item) {
//            return $item->quantidade * $item->price;
//        });
//
//        $taxa = $items[0]->clientes[0]->taxa ?? 0;
//
//        $total = $subTotal-$this->discount;
//
//        return  array("total" => $total, "subTotal" => $subTotal, "taxa" => $taxa);
//    }

    /**
     * Função pincipal com os dados da venda
    */
    public function loadCartItems()
    {
        $this->cartItems =  Carts::with(['variations.produtos','clientes'])
            ->where('user_id',  $this->userId )
            ->where('status',  'ABERTO' )
            ->orderBy('id','desc')
            ->get();

        // Calculando o total
        $subTotal =  $this->cartItems->sum(function ($item) {
            return $item->quantidade * $item->price;
        });

        //taxa do cliente
        $taxa = $this->cartItems[0]->clientes[0]->taxa ?? 0;

        //pega o desconto da venda
        foreach ($this->cartItems as $product) {
            $this->discount = 0;
            if($product['quantidade'] < 5){
                //$this->discount += $product['variations'][0]['valor_varejo'] * $product['variations'][0]['percentage']/100;
                $this->discount += $product['price'] * $product['variations'][0]['percentage']/100;
            }
        }

        //total geral
        $total = $subTotal-$this->discount;

        //Retorna os totais
       // $totais = $this->getTotalCartTraitByUser($this->cartItems);
        $this->total = $total;
        $this->subTotal = $subTotal;
        //$this->taxa = $taxa ;
        //$this->frete = $taxa;

    }

    /**
     * Retorna o total de itens do carrinho
     * @param $cartItems
     */
    public function totalItens($cartItems){
        $this->totalItens = count($cartItems);
    }

    /**
     * Salva a venda em definitivo
     * @param $data
     */
    public function storeSaleTrait($data)
    {
        DB::beginTransaction();

       // dd($data);

        $status = 'PAGO'; // valor que você deseja definir para o campo status
        $clienteId = null;
        $productsData = [];
        $productVariations =[];
        try {
            //$cartTotal = $this->getTotalCartTraitByUser()['total'];
            if ($this->cartItems->isNotEmpty() && optional($this->cartItems->first()->clientes)->isNotEmpty()){
                //$cartTotal += $this->cartItems[0]->clientes[0]->taxa;
                $clienteId = $this->cartItems[0]->clientes[0]->id;
            }

            $sale = ["codigo_venda" => $data["codigo_venda"],
                "loja_id" =>  $data["loja_id"],
                "valor_total" => $this->total,
                "usuario_id" =>  Auth::id(),//isset($dados["usuario_id"]) ? $dados["usuario_id"] : 3,
                "cliente_id" =>  $clienteId,
                "tipo_venda_id" => (int)$data['tipoVenda'],
                "forma_entrega_id" => $data['forma_entrega']
            ];
            dd($sale);
            //Salva a venda
            $sale = Vendas::create($sale);

            //Pega a venda
            //$this->cartItems = $this->getClientItemCartTrait();
            //$this->loadCartItems();

            // extrai os IDs dos itens do carrinho para atualizar a tabela Carts, com venda_id e Status PAGO
            $ids = $this->cartItems->pluck('id')->toArray();

            foreach ($this->cartItems as $produto) {
                $productsData[] = [
                    'venda_id' => $sale->id,
                    'codigo_produto' => $produto['codigo_produto'],
                    'descricao' => $produto['name'],
                    'valor_produto' => floatval($produto['price']),
                    'quantidade' => $produto['quantidade'],
                    'troca' => false,
                    'fornecedor_id' => $produto['variations'][0]['fornecedor'],
                    'categoria_id' => $produto['variations'][0]['produtos']['categoria_id']
                ];
                // Adiciona os ids das variações para atualizar o estoque
                array_push($productVariations, ['id' => $produto['variations'][0]['id'],'quantidade' => $produto['quantidade']
                ]);
            }

            // Salva os produtos da venda em massa
            $inserted = VendaProdutos::insert($productsData);
            if ($inserted) {
                // Verifica se há IDs para atualizar e atualiza a tabela de Carts
                if (!empty($ids)) {
                    Carts::whereIn('id', $ids)->update(['venda_id' => $sale->id, 'status' => $status]);
                }

                // Atualiza as quantidades em estoque
                foreach ($productVariations as $variation) {
                    $produtoVariation = ProdutoVariacao::find($variation['id']);
                    if ($produtoVariation) {
                        $produtoVariation->decrement('quantidade', $variation['quantidade']);
                    }
                }

                // Recupera todas as taxas necessárias de uma vez
                $taxas = TaxaCartao::whereIn('forma_id', [$data['forma_pgto']])->pluck('valor_taxa', 'forma_id')->toArray();

                // Prepara os dados para inserção em massa
                $paymentTypesData = [];
                //for ($i = 0; $i < $totalPayment; $i++) {
                    $paymentTypesData[] = [
                        'venda_id' => $sale->id,
                        'forma_pagamento_id' => $data['forma_pgto'],
                        'valor_pgto' => $this->total,
                        'taxa' => $taxas[$data['forma_pgto']] ?? 0
                    ];
                //}
                // Inserção em massa
                VendasProdutosTipoPagamento::insert($paymentTypesData);


                //VendasProdutosDesconto::insert();

            }


            // Confirmar a transação
            DB::commit();
        } catch (\Exception $e) {
            // Reverter a transação em caso de erro
            DB::rollBack();
            $this->emit("message", " Falha ao fechar venda. ". $e->getMessage(), IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
        }
        $this->emit("message", "Venda realizada com sucesso. ", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,true);
    }


}
