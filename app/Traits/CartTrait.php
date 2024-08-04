<?php
namespace App\Traits;

use App\Constants\IconConstants;
use App\Http\Models\Carts;
use App\Http\Models\Cashback;
use App\Http\Models\FormaPagamento;
use App\Http\Models\Loja;
use App\Http\Models\ProdutoVariacao;
use App\Http\Models\TaxaCartao;
use App\Http\Models\VendaProdutos;
use App\Http\Models\Vendas;
use App\Http\Models\VendasCashBack;
use App\Http\Models\VendasProdutosDesconto;
use App\Http\Models\VendasProdutosEntrega;
use App\Http\Models\VendasProdutosTipoPagamento;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use PHPUnit\Framework\Constraint\Count;


trait CartTrait {

    /**
     * Adiciona item ao carrinho
     * @param $barcode
     * @param $productVariationId
     */
    public function addToCartTrait($barcode,$productVariationId)
    {

        //$cartItem = Carts::where('product_id', $productId)->first();
        $cartItem = Carts::with('clientes')
            ->where('user_id', $this->userId)
            ->where('produto_variation_id', $productVariationId)
            ->where('status' , 'ABERTO')->first();

        //busa o produto para inserir no carrinho pelo seu codigo
        $produto = ProdutoVariacao::with('images','produtos')
            ->where("subcodigo",$barcode)
            ->where("status",true)
            ->first();
       // dd($cartItem );

        if ($cartItem) {
            if($cartItem->quantidade+1 > $produto->quantidade)
            {
                $this->emit("message", "Estoque insuficiente! Quantidade total disponível [$cartItem->quantidade]", IconConstants::ICON_WARNING,IconConstants::COLOR_RED);
                return;
            }
            $cartItem->increment('quantidade');
        } else {
            $cliente_id = null;
            //verifica se tem venda aberta e se tem cliente associado de ao menos 1 item no carrinho
            $cartItemCliente = Carts::with('clientes')
                ->where('user_id', $this->userId())
                ->where('status' , 'ABERTO')->first();

            //dd($cartItemCliente);
            if ($cartItemCliente && $cartItemCliente->clientes->isNotEmpty()) {
                $cliente_id = $cartItemCliente->clientes[0]->id;
            }

            $carts=[
                'user_id' => $this->userId,
                'produto_variation_id' => $produto->id,
                'name' => $produto['produtos'][0]->descricao ." - " . $produto->variacao,
                'price' => $produto->valor_varejo,
                'codigo_produto' => $produto->subcodigo,
                'quantidade' => 1,
                'imagem' => count($produto->images) > 0 ? $produto->images[0]->path : "",
                'cliente_id' =>$cliente_id
            ];

            Carts::create($carts);
        }
        $this->loadCartItemsTrait();
        $this->emit("message", "Item adicionado com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
        $this->emitTo('incluir-cart','cartUpdated');
        $this->emitTo('total-sale','totalSaleVendaUpdated','');
        $this->emitTo('incluir-totais','totaisUpdated');
        $this->emit('refresh', true);
        $this->barcode = "";

    }

    /**
     * Busca na base pelo cádigo do produto e adiciona no componente CART
     * @param $barcode
     * @param int $cant
     */

    /*public function ScanearCode($barcode, $cant = 1)
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

                 if (!$this->cartItems->isEmpty()) {
                     if (!$this->items[0]->clientes->isEmpty()) {
                         $this->carts->cliente_id = $this->items[0]->clientes[0]->id;
                     }
                 }


                 $this->emit("message", "Produto Adicionado!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,false,true);
         }
     }*/

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
    public function increaseQuantityTrait($product_id, $cant = 1)
    {
            $product = Carts::with('variations')->where('produto_variation_id', $product_id)
                    ->where('user_id',$this->userId )
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
                    $this->emit("message", "Quantidade adicionada com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
                    $this->emitTo('incluir-cart','cartUpdated');
                    $this->emitTo('total-sale','totalSaleVendaUpdated','');
                    $this->emitTo('incluir-totais','totaisUpdated');
                   // $this->emitTo('cart-component','atualizarCarrinho');
                }else{
                    $this->emit("message", "Não foi possivel atualizar a venda!", IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
                }
            }
    }

    /**
     * Decrementa a quantidade de produto no componente CART
     * @param $product_id
     */
    public function decreaseQuantityTrait($product_id){

        $product = Carts::with('variations')->where('produto_variation_id', $product_id)
            ->where('user_id',$this->userId )
            ->where('status', 'ABERTO')
            ->first();

        if($product) {
            $product->price = $product->variations[0]->valor_atacado_10un;

            if ((($product->quantity) - 1) < 5) {
                $product->price = $product->variations[0]->valor_varejo;
            }

            $product->quantidade--;

            if($product->update()){
                $this->emit("message", "Quantidade removida com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
                $this->emitTo('incluir-cart','cartUpdated');
                $this->emitTo('total-sale','totalSaleVendaUpdated','');
                //$this->emitTo('cart-component','atualizarCarrinho');
                $this->emitTo('incluir-totais','totaisUpdated');
            }else{
                $this->emit("message", "Não foi possivel atualizar a venda!", IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
            }

        }
    }

    /**
     * Passsa para o componente CART a solicitação para remover o item
     * @param $productId
     */
//    public function removeCartItem($productId)
//    {
//        $delete = Carts::find($productId)->delete();
//
//        if($delete){
//            $this->emit("message", "Produto removido!", IconConstants::ICON_REMOVE_CART,IconConstants::COLOR_RED);
//            $this->emit('refresh', true);
//        }else{
//            $this->emit("message", "Não foi possivel remover o produto!", IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
//        }
//    }



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
                    where('user_id', $this->userId )
                    ->where('cliente_id', $cliente_id)
                    ->where('status' , 'ABERTO')
                    ->get();

        foreach ($items as $item){
            $item->cliente_id = null;
            $item->save();
        }
       // $this->emit('global-msg', 'Cliente removido da venda, com sucesso!!');
    }

    /**
     * Remove o produto do carrinho
     * @param $cartItemId
     */
    public function removeFromCartTrait($cartItemId)
    {
        Carts::find($cartItemId)->delete();

        $this->loadCartItemsTrait();
        $this->emit("message", "Item removido com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
        $this->emitTo('incluir-cart','cartUpdated');
        $this->emitTo('total-sale','totalSaleVendaUpdated','');
        //$this->emitTo('cart-component','atualizarCarrinho');
        $this->emitTo('incluir-totais','totaisUpdated');


        if(count($this->cartItems) == 0)
            $this->emit('refresh', true);
    }


    /**
     * Função pincipal com os dados da venda
    */
    public function loadCartItemsTrait()
    {
        $this->cartItems =  Carts::with(['variations.produtos','clientes','cashback'])
            ->where('user_id',  $this->userId  )
            ->where('status',  'ABERTO' )
            ->orderBy('id','desc')
            ->get();

        /**
         * Pega o cashback
         * */
        $cashbacks = $this->cashback();
        if($cashbacks !== null){
            $this->cashback =0;
            foreach ($cashbacks as $cashback) {
                $this->cashback += $cashback->valor;
            }
        }

        //verica se tem desconto
        $this->discount();
        //Retorna os totais
        $this->subTotal();
        //total geral
        $this->total();//$this->total = $this->subTotal()-$this->discount;

        $this->totalItens = count($this->cartItems);
    }

    /***
     * Retorna o total da venda
     */
    public function total(){
        $this->total =  $this->subTotal -$this->discount - $this->cashback;
    }

    /***
     * Retorna o subtotal da venda
     */
    public function subTotal(){
        // Calculando o subtotal
        $this->subTotal = $this->cartItems->sum(function ($item) {
            return $item->quantidade * $item->price;
        });
    }

    /***
     * Retorna o desconto do produto na venda
     */
    public function discount(){
        $this->discount = 0;
        //pega o desconto da venda
        foreach ($this->cartItems as $product) {
            if($product['quantidade'] < 5 && \floatval($product['variations'][0]['percentage'] > 0)){
                //$this->discount += $product['variations'][0]['valor_varejo'] * $product['variations'][0]['percentage']/100;
                $this->discount += $product['price'] * $product['variations'][0]['percentage']/100;
            }
        }
    }


    public function cashback(){
        foreach ($this->cartItems as $cartItem) {
            return $cartItem->cashback->filter(function ($cashback) {
                return $cashback->status == false;
            });
        }
    }

    /**
     * Salva a venda em definitivo
     * @param $data
     */
    public function storeSaleTrait($data)
    {
        DB::beginTransaction();
        $this->printSale($data);

        $status = 'PAGO'; // valor que você deseja definir para o campo status
        $clienteId = null;
        $productsData = [];
        $productVariations =[];
        //$this->total =\floatval(\number_format($this->total,2));

        try {
            //$cartTotal = $this->getTotalCartTraitByUser()['total'];
            if ($this->cartItems->isNotEmpty() && optional($this->cartItems->first()->clientes)->isNotEmpty()){
                //$cartTotal += $this->cartItems[0]->clientes[0]->taxa;
                $clienteId = $this->cartItems[0]->clientes[0]->id;
            }

            $sale = ["codigo_venda" => $data["codigo_venda"],
                "loja_id" =>  $data["loja_id"],
                "valor_total" => $this->subTotal,
                "usuario_id" =>  $this->userId ,//isset($dados["usuario_id"]) ? $dados["usuario_id"] : 3,
                "cliente_id" =>  $clienteId,
                "tipo_venda_id" => (int)$data['tipoVenda'],
                "forma_entrega_id" => (int)$data['forma_entrega']
            ];

            //Salva a venda
            $sale = Vendas::create($sale);

            // extrai os IDs dos itens do carrinho para atualizar a tabela Carts, com venda_id e Status PAGO
            $ids = $this->cartItems->pluck('id')->toArray();

            //dd($this->cartItems);

            foreach ($this->cartItems as $produto) {

                $percentual_desconto = $produto['variations'][0]['percentage'];
                if($produto['quantidade'] > 5 || count($this->cartItems) >= 10  ){
                    $percentual_desconto = 0;
                }

                $productsData[] = [
                    'venda_id' => $sale->id,
                    'codigo_produto' => $produto['codigo_produto'],
                    'descricao' => $produto['name'],
                    'valor_produto' => floatval($produto['price']),
                    'quantidade' => $produto['quantidade'],
                    'percentual_desconto' => $percentual_desconto,
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
                $taxes = TaxaCartao::whereIn('forma_id', [$data['forma_pgto']])->pluck('valor_taxa', 'forma_id')->toArray();

                // Prepara os dados para gravar  forma de pagamento
                $paymentTypesData = [];
                //for ($i = 0; $i < $totalPayment; $i++) {
                    $paymentTypesData[] = [
                        'venda_id' => $sale->id,
                        'forma_pagamento_id' => $data['forma_pgto'],
                        'valor_pgto' => $this->total,
                        'taxa' => $taxes[$data['forma_pgto']] ?? 0
                    ];
                //}
                VendasProdutosTipoPagamento::insert($paymentTypesData);

                 // Prepara os dados para inserção dos descontos
                 $saleDiscountData[] = [
                    'venda_id' => $sale->id,
                    'valor_desconto' => $this->discount,
                    'valor_recebido' =>  $data["valor_dinheiro"] ? $data["valor_dinheiro"] : 0,
                    'valor_percentual' => 0
                ];

                VendasProdutosDesconto::insert($saleDiscountData);

                // Prepara os dados para inserção da entrega
                $saleEntregaData[] = [
                    'venda_id' => $sale->id,
                    'forma_id' => $this->formaId,
                    'valor_entrega' => $this->frete,
                ];

                VendasProdutosEntrega::insert($saleEntregaData);


                //Salva o cashback
                if(!empty($this->cartItems[0]['clientes'])){
                    $cashbacks = Cashback::all();
                    $taxa = 0.05;
                    foreach ($cashbacks as $valor) {
                        if ($valor->valor < $sale->valor_total-$this->discount) {
                            $taxa = $valor->taxa;
                        }
                    }
                    //DB::rollBack();
                    //dd([$sale->valor_total,$this->discount,$this->total]);
                    $valor_cashback = ($sale->valor_total-$this->discount * $taxa) / 100;

                    $cashbackData[] = [
                        'cliente_id' => $this->cartItems[0]['clientes'][0]['id'],
                        'venda_id' => $sale->id,
                        'valor' => $valor_cashback
                    ];

                    /**
                     * Pega os dados dos cashback ativos do cliente
                     * */
                    $cashback_ids = [];
                    $cashbacks = $this->cashback();
                    if($cashbacks !== null){
                        foreach ($this->cashback() as $cashback) {
                            array_push($cashback_ids,$cashback->id);
                        }
                        //atualiza os cashbacks antigos para usado
                        VendasCashBack::whereIn('id',$cashback_ids)->update(['status' => true]);
                    }
                    //Salva novo cashback
                    VendasCashBack::insert($cashbackData);
                }

            }
            // Confirmar a transação
            DB::commit();

        } catch (\Exception $e) {
            // Reverter a transação em caso de erro
            DB::rollBack();
            $this->emit("message", " Falha ao fechar venda. ". $e->getMessage(), IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
        } finally {
            $this->printSale($data);
            $this->emit("message", "Venda realizada com sucesso. ", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,true);
        }
    }


    private function printer($texto){
        try {
            $connector = new NetworkPrintConnector("192.168.0.200", 9100);

            /* Print a "Hello world" receipt" */
            $printer = new Printer($connector);

            $printer -> initialize();
            $printer -> setFont(1);
            $printer -> setLineSpacing(4);
            $printer -> setJustification(0);
            $printer -> selectCharacterTable(3);

            $printer -> text($texto);
            $printer ->feed(2);
            $printer -> cut();

            /* Close printer */
            $printer -> close();

        } catch (\Exception $e) {
           // echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
            $this->emit("message", "Error. " .  $e -> getMessage() , IconConstants::ICON_ERROR,IconConstants::COLOR_RED);

        }
    }

    /**
     * Imprimir a venda
     * @param $data
     */
    public function printSale($data){
        //dd($data);
        $cupom = '';
        try {

            $enterprise = Loja::where('id',$data['loja_id'])->where("status",true)->first();
            $formaPagamento = FormaPagamento::where('id',$data['forma_pgto'])->where("status",true)->first();
            $vendedor = auth()->user()->sexo =='M' ? "Vendedor: " : "Vendedora: ";

            $formatter = new \NumberFormatter('pt_BR',  \NumberFormatter::CURRENCY);

            $conteudoCupom = '';
            foreach ($this->cartItems as $item){
                $conteudoCupom .= $this->formataEspacos($item->name,25,'D');
                $conteudoCupom .= $formatter->formatCurrency($item->price, 'BRL') . '      ';
                $conteudoCupom .= $item->quantidade . '     ';
                $conteudoCupom .= $formatter->formatCurrency($item->price*$item->quantidade, 'BRL') . "\n\r";
            }

          /*  $cupom =  "                       " .$enterprise->razao . "\n\r"
            . "      " . $enterprise->endereco . " - " . $enterprise->local . "\n\r";
            $this->printer($cupom);
            return;*/


            $cupom =  "                       " .$enterprise->razao . "\n\r"
                . "      " . $enterprise->endereco . " - " . $enterprise->local . "\n\r"
                . "      Data: " . Carbon::now()->format("d/m/Y H:i:s") . " Tel: " . $enterprise->telefone . "\n\r"
                . "                    Santa Cruz, Rio de Janeiro-RJ \n\r"
                . "----------------------------------------------------------------\n\r"
                . "                        CUPOM NÃO FISCAL                       \n\r"
                . "----------------------------------------------------------------\n\r"
                . "ITEM DESCRIÇÃO            VALOR           QTD         TOTAL     \n\r"
                . "----------------------------------------------------------------\n\r"
                . $conteudoCupom
                . "----------------------------------------------------------------\n\r"
                . "TOTAL DE ITENS                                        " . $this->totalItens . "\n\r"
                . "SUB TOTAL                                             " . $formatter->formatCurrency($this->subTotal, 'BRL') . "\n\r"
                . "DESCONTO                                              " . $formatter->formatCurrency($this->discount, 'BRL') . "\n\r"
                . "CASHBACK                                              " . $formatter->formatCurrency($this->cashback, 'BRL'). "\n\r"
                . "TOTAL                                                 " . $formatter->formatCurrency($this->subTotal-$this->discount-$this->cashback, 'BRL'). "\n\r"
                . "FRETE                                                 " . $formatter->formatCurrency($this->frete, 'BRL'). "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "VALOR A PAGAR                                    " . $formatter->formatCurrency($this->total, 'BRL'). "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "FORMA DE PAGAMENTO                             $formaPagamento->nome \n\r"
                . "VALOR RECEBIDO                                   " . $formatter->formatCurrency($this->dinheiro, 'BRL'). "\n\r"
                . "TROCO                                            " . $formatter->formatCurrency($this->troco, 'BRL') . "\n\r"
                . "----------------------------------------------------------------\n\r"
                . $vendedor.auth()->user()->nome ."      |        Codigo Venda: ".$data['codigo_venda'] . "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "     PRAZO DE 7 DIAS PARA TROCA MEDIANTE ESTA NOTA       \n\r"
                . "      (somente com defeito de fabrica ou sem uso)        \n\r"
                . "  Nao trocamos produtos de promocao,peliculas,esmaltes,  \n\r"
                . "                   cilios,pincas,colas!                  \n\r"
                . "            Eletronicos 30 dias para troca!              \n\r"
                . "                Nao devolvemos dinheiro!                 \n\r"
                . "                OBRIGADA E VOLTE SEMPRE                  \n\r\n\r \f ";

        } catch (\Exception $e) {
              $this->emit("global-error", " Falha na impressão da venda. ". $e->getMessage());
        } finally {
            $this->printer($cupom);
            $this->emit("message", "Impressão da Venda realizada com sucesso! ", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,false);
        }

    }

    private function formataEspacos($string, int $total,  $lado) {
		$totalLen = strlen($string);

		$tam_valor = $total - $totalLen;
		$espacos = "";

        for ($i = 0; $i < $tam_valor; $i++) {
			$espacos .= " ";
		}

        if ($lado == "D") {
            $retorno = $string.$espacos;
        } else {
            $retorno = $espacos.$string;
        }
        return $retorno;
        }

    private function comando() {

        $GS = "\x1d";//chr(29);
        $ESC = "\x1b";//chr(27);

        $COMMAND  = $ESC." @ ";
        $COMMAND .= $ESC." M ".chr(1); // Select character font: Font B 1(EPSON) ou 49( BEMATECH)
		$COMMAND .= $ESC." R ".chr(0); //Select an international character set EPSON / BEMATECH
		$COMMAND .= $ESC." a ".chr(0); // Select justification: Left justification n = 0, "0": Left justificationn = "1": Centeringn = 2, "2": Right justification
		$COMMAND .= $ESC." t ".chr(2); //tabela de código 3 PC860: Portuguese

		$COMMAND .= $GS." V " . chr(66) . chr(0);// cut partial

		return $COMMAND;
	}

    public function getImageUrl($item)
    {
        $url = 'http://127.0.0.1/api-loja-new-git/public/storage/produtos';
        if($item->imagem){
            return $url.'/'.$item->imagem;
        }else{
            return $url.'/not-image.png';
        }
    }

    public function userId(){
        return Auth::guard('customer')->id();
    }
}
