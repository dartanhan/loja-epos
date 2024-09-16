<?php
namespace App\Traits;

use App\Constants\IconConstants;
use App\Enums\StatusVenda;
use App\Http\Models\Carts;
use App\Http\Models\Cashback;
use App\Http\Models\FormaPagamento;
use App\Http\Models\Loja;
use App\Http\Models\ProdutoVariacao;
use App\Http\Models\TaxaCartao;
use App\Http\Models\Usuario;
use App\Http\Models\VendaProdutos;
use App\Http\Models\Vendas;
use App\Http\Models\VendasCashBack;
use App\Http\Models\VendasProdutosDesconto;
use App\Http\Models\VendasProdutosEntrega;
use App\Http\Models\VendasProdutosTipoPagamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;


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

        //busca o produto para inserir no carrinho pelo seu codigo
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
                ->where('user_id', $this->userId)
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
            //dd($carts);

           Carts::create($carts);
        }
        $this->loadCartItemsTrait();

        $this->emit("message", "Item adicionado com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
        $this->emitTo('incluir-cart','cartUpdated');
        $this->emitTo('total-sale','totalSaleVendaUpdated','', $this->hasCashback);
        $this->emitTo('incluir-totais','totaisUpdated');
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
        $this->codeSale = "KN". sprintf("%05d", $numeroAleatorio);;
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
                    $this->emitTo('total-sale','totalSaleVendaUpdated','', $this->hasCashback);
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
                $this->emitTo('total-sale','totalSaleVendaUpdated','', $this->hasCashback);
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
     * @param $data
     */
    public function trashCartTrait($data) {

        $deletedRows  = Carts::where('user_id',$data['user_id'])->where('status' , StatusVenda::ABERTO)->delete();

        if ($deletedRows > 0) {
            $this->emit("message", "Venda cancelada com sucesso!", IconConstants::ICON_SUCCESS, IconConstants::COLOR_GREEN,true,true);
        }
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
        $this->emitTo('total-sale','totalSaleVendaUpdated','', $this->hasCashback);
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
            ->whereIn('status',  ['ABERTO'])
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
        $this->getClientId();
    }

    /***
     * Retorna o total da venda
     */
    public function total(){
        $this->total =  $this->subTotal - $this->discount;

        //Caso o cashback seja true dimiminui da venda o valor
        if($this->hasCashback){
            $this->total =  $this->subTotal - $this->discount - $this->cashback;
        }
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

    /***
     * Retorna o status do cashback
     */
    public function cashback(){
        foreach ($this->cartItems as $cartItem) {
            return $cartItem->cashback->filter(function ($cashback) {
                return $cashback->status == false;
            });
        }
    }

    private function getClientId(){
        $this->clienteId = 0;
        if ($this->cartItems->isNotEmpty() && optional($this->cartItems->first()->clientes)->isNotEmpty()){
            //$cartTotal += $this->cartItems[0]->clientes[0]->taxa;
            $this->clienteId = $this->cartItems[0]->clientes[0]->id;
            $this->clienteName = $this->cartItems[0]->clientes[0]->nome;
        }
    }
    /**
     * Salva a venda em definitivo
     * @param $data
     */
    public function storeSaleTrait($data)
    {
        //  $this->printSale($data);
        DB::beginTransaction();

        if($data['status'] == StatusVenda::PENDENTE) {
            $ids = $this->cartItems->pluck('id')->toArray();
            //dd($ids);
            Carts::whereIn('id', $ids)->update(['status' => $data['status']]);

            DB::commit();
            $this->emit("message", "Venda salva com sucesso!. ", IconConstants::ICON_SUCCESS, IconConstants::COLOR_GREEN, true);

        }elseif ($data['status'] == StatusVenda::TROCA){
         dd($data);
        }else{
            dd("teste");
            $clienteId = null;
            $productsData = [];
            $productVariations =[];
            //$this->total =\floatval(\number_format($this->total,2));

            try {
                //$cartTotal = $this->getTotalCartTraitByUser()['total'];
//            if ($this->cartItems->isNotEmpty() && optional($this->cartItems->first()->clientes)->isNotEmpty()){
//                //$cartTotal += $this->cartItems[0]->clientes[0]->taxa;
//                $clienteId = $this->cartItems[0]->clientes[0]->id;
//            }
                /**
                 * Carrega o Id do cliente e o Nome para imprimir no cupom
                */
                $this->getClientId();

                $sale = ["codigo_venda" => $data["codigo_venda"],
                    "loja_id" =>  $data["loja_id"],
                    "valor_total" => $this->subTotal,
                    "usuario_id" =>  $this->userId ,//isset($dados["usuario_id"]) ? $dados["usuario_id"] : 3,
                    "cliente_id" =>  $this->clienteId,
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
                        Carts::whereIn('id', $ids)->update(['venda_id' => $sale->id, 'status' => StatusVenda::PAGO]);
                    }

                    // Atualiza as quantidades em estoque
                    foreach ($productVariations as $variation) {
                        $produtoVariation = ProdutoVariacao::find($variation['id']);
                        if ($produtoVariation) {
                            $produtoVariation->decrement('quantidade', $variation['quantidade']);
                        }
                    }

                    // Recupera todas as taxas necessárias de uma vez
                    //$taxes = TaxaCartao::whereIn('forma_id', [$data['forma_pgto']['id']])->pluck('valor_taxa', 'forma_id')->toArray();

                    // Prepara os dados para gravar  forma de pagamento
                    $paymentTypesData = [];
                    $paymentTypes = [];
                    for ($i = 0; $i < count($data['forma_pgto']); $i++) {
                        $paymentTypes[] = [
                            'nome' => FormaPagamento::find($data['forma_pgto'][$i]['id'])->nome,
                            'valor_pgto' => $data['forma_pgto'][$i]['valor']
                        ];

                        $paymentTypesData[] = [
                            'venda_id' => $sale->id,
                            'forma_pagamento_id' => $data['forma_pgto'][$i]['id'],
                            'valor_pgto' => $data['forma_pgto'][$i]['valor'],
                            'taxa' => (float)TaxaCartao::where('forma_id',$data['forma_pgto'][$i]['id'])->first()->valor_taxa
                        ];
                    }
                    $data['paymentTypes'] = $paymentTypes;

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
                    //if (isset($this->cartItems[0]['clientes']) && is_array($this->cartItems[0]['clientes']) && !empty($this->cartItems[0]['clientes'])) {
                    //Verificando Se a Chave Existe no Array
                    if (array_key_exists(0, $this->cartItems) && array_key_exists('clientes', $this->cartItems[0])) {
                        //dd($this->cartItems[0]['clientes']);
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

                $this->printSale($data);
                $this->emit("message", "Venda realizada com sucesso. ", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,true);

            } catch (\Exception $e) {
                // Reverter a transação em caso de erro
                DB::rollBack();
                $this->emit("message", " Falha ao fechar venda. ". $e->getMessage(), IconConstants::ICON_ERROR,IconConstants::COLOR_RED);
            }
        }


    }

    /**
     * Formata a saida do cupom para imprimir a venda
     * @param $data
     */
    public function printSale($data){
        //dd($data, $this->cartItems, $this->clienteName);

        try {

            $enterprise = Loja::where('id',$data['loja_id'])->where("status",true)->first();
            //$formaPagamento = FormaPagamento::where('id',$data['forma_pgto'])->where("status",true)->first();
            $vendedor = auth()->user()->sexo =='M' ? "Vendedor: " : "Vendedora: ";

            $formatter = new \NumberFormatter('pt_BR',  \NumberFormatter::CURRENCY);

            $conteudoCupom = '';
            foreach ($this->cartItems as $item){
                $conteudoCupom .= $this->formataEspacos($item->name,36,'D');
                $conteudoCupom .= $formatter->formatCurrency($item->price, 'BRL') . '      ';
                $conteudoCupom .= $item->quantidade . '     ';
                $conteudoCupom .= $formatter->formatCurrency($item->price*$item->quantidade, 'BRL') . "\n\r";
            }

            /**
             * Forma de pagamento da venda
             */
            $forma_nome ='';
            foreach ($data['paymentTypes'] as $forma){
                $forma_nome .= $this->formataEspacos($forma['nome'],50,'D') . '    ' . $formatter->formatCurrency($forma['valor_pgto'], 'BRL') ."\n\r";
            }

            $cupom = '';
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
                . "TOTAL                                                 " . $formatter->formatCurrency($this->total, 'BRL'). "\n\r"
                . "FRETE                                                 " . $formatter->formatCurrency($this->frete, 'BRL'). "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "VALOR A PAGAR                                         " . $formatter->formatCurrency($this->total, 'BRL'). "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "FORMA DE PAGAMENTO \n\r" .$forma_nome."\n\r";

                if($this->dinheiro > 0){
                    $cupom .= "VALOR RECEBIDO                            " . $formatter->formatCurrency($this->dinheiro, 'BRL'). "\n\r";
                    $cupom .= "TROCO                                     " . $formatter->formatCurrency($this->troco, 'BRL') . "\n\r";
                }

                $cupom .= $this->footerCupon($vendedor.auth()->user()->nome, $data['codigo_venda'],$this->clienteName);

            $this->printer($cupom);
            $this->emit("message", "Impressão da Venda realizada com sucesso! ", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,false);

        } catch (\Exception $e) {
              $this->emit("global-error", " Falha na impressão da venda. ". $e->getMessage());
        }

    }

    /**
     * Re-imprime a venda
     * @param $sale
     */
    public function reprintSaleTrait($sale){
        //dd($sale);

        try {
            $formatter = new \NumberFormatter('pt_BR',  \NumberFormatter::CURRENCY);
            /**
             * Dados da loja
            */
            $enterprise = Loja::where('id',$sale->loja_id)->where("status",true)->first();

            /**
             * Forma de pagamento da venda
            */
            $forma_nome ='';
            foreach ($sale->forma_pgto as $forma){
                $forma_nome .= $this->formataEspacos($forma->payments->nome,50,'D') . '    ' . $formatter->formatCurrency($forma->valor_pgto, 'BRL') ."\n\r";
            }
            /**
             * pega o usuório da venda
            */
            $user = Usuario::where('id', $sale->usuario_id)->first(); //auth()->user()->sexo =='M' ? "Vendedor: " : "Vendedora: ";
            $vendedor = $user->sexo =='M' ? "Vendedor: " : "Vendedora: ";
            $vendedor .= $user->nome;

            /**
             * pega o cliente da venda
             */
            $cliente = '';
            if ($this->sale && isset($this->sale->cliente) && count($this->sale->cliente) > 0) {
                $cliente = $this->sale->cliente[0]->nome;
            }


            $conteudoCupom = '';
            $subTotal = 0;
            foreach ($sale->products as $item){
                $conteudoCupom .= $this->formataEspacos($item->descricao,37,'D');
                $conteudoCupom .= $this->formataEspacos($formatter->formatCurrency($item->valor_produto, 'BRL') ,9,'D'). '      ';
                $conteudoCupom .= $item->quantidade . '    ';
                $conteudoCupom .= $formatter->formatCurrency($item->valor_produto*$item->quantidade, 'BRL') . "\n\r";


                $subTotal += $item->valor_produto*$item->quantidade;
            }

            $desconto = $sale->desconto[0]->valor_desconto;
            $cashback = $sale->cashback[0]->valor ?? 0;
            $frete  = $sale->frete[0]->valor_entrega ?? 0;

            $total = $subTotal - $desconto - $cashback;
            $total_pagar = $total + $frete;

            $cupom = '';
            $cupom =  "                       " .$enterprise->razao . "\n\r"
                . "      " . $enterprise->endereco . " - " . $enterprise->local . "\n\r"
                . "      Data: " . Carbon::now()->format("d/m/Y H:i:s") . " Tel: " . $enterprise->telefone . "\n\r"
                . "                    Santa Cruz, Rio de Janeiro-RJ \n\r"
                . "----------------------------------------------------------------\n\r"
                . "                        CUPOM NÃO FISCAL                       \n\r"
                . "----------------------------------------------------------------\n\r"
                . "ITEM DESCRIÇÃO                         VALOR      QTD    TOTAL  \n\r"
                . "----------------------------------------------------------------\n\r"
                . $conteudoCupom
                . "----------------------------------------------------------------\n\r"
                . "TOTAL DE ITENS                                        " . count($sale->products) . "\n\r"
                . "SUB TOTAL                                             " . $formatter->formatCurrency($subTotal, 'BRL') . "\n\r"
                . "DESCONTO                                              " . $formatter->formatCurrency($desconto, 'BRL') . "\n\r"
                . "CASHBACK                                              " . $formatter->formatCurrency($cashback, 'BRL'). "\n\r"
                . "TOTAL                                                 " . $formatter->formatCurrency($total, 'BRL'). "\n\r"
                . "FRETE                                                 " . $formatter->formatCurrency($frete, 'BRL'). "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "VALOR A PAGAR                                         " . $formatter->formatCurrency($total_pagar, 'BRL'). "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "FORMA DE PAGAMENTO \n\r" .$forma_nome."\n\r";

            if($sale->forma_pgto[0]->payments->slug == 'dinheiro'){
                $cupom .= "VALOR RECEBIDO" . $this->formataEspacos($formatter->formatCurrency($sale->desconto[0]->valor_recebido, 'BRL'),49,'E'). "\n\r";
                $cupom .= "TROCO" .$this->formataEspacos($formatter->formatCurrency($total - $sale->desconto[0]->valor_recebido, 'BRL'),58,'E') . "\n\r";
            }

            $cupom .= $this->footerCupon($vendedor, $sale->codigo_venda,$cliente);

           // dd($cupom);

              $this->printer($cupom);
              $this->emit("message", "Re-Impressão da Venda realizada com sucesso! ", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,false);
        } catch (\Exception $e) {
            $this->emit("global-error", " Falha na reimpressão da venda. ". $e->getMessage());
        }
    }

    /**
     * Composição da parte de baixo do cupom
     * @param $vendedor
     * @param $codigo_venda
     * @param $cliente
     * @return string
     */
    private function footerCupon($vendedor, $codigo_venda,$cliente ){
            $cupom_cli = '';
            if($cliente){
                $cupom_cli = "Cliente: ".$cliente;
            }
            return "----------------------------------------------------------------\n\r"
                . $vendedor ."            |        Codigo Venda: ".$codigo_venda . "\n\r"
                . $cupom_cli                                                     . "\n\r"
                . "----------------------------------------------------------------\n\r"
                . "     PRAZO DE 7 DIAS PARA TROCA MEDIANTE ESTA NOTA       \n\r"
                . "      (somente com defeito de fábrica ou sem uso)        \n\r"
                . "  Não trocamos produtos de promocao,peliculas,esmaltes,  \n\r"
                . "                   cilios,pincas,colas!                  \n\r"
                . "            Eletrônicos 30 dias para troca!              \n\r"
                . "                Não devolvemos dinheiro!                 \n\r"
                . "                OBRIGADA E VOLTE SEMPRE                  \n\r ";
    }

    /**
     * Imprime a venda
     * @param $body
     */
    private function printer($body){

        try {
           // $connector = new NetworkPrintConnector("192.168.0.200", 9100);
            //$connector = new WindowsPrintConnector("smb://computer/printer");
            //$connector = new WindowsPrintConnector("smb://DESKTOP-KOC02LS/L4260Series");
            //$connector = new WindowsPrintConnector("EPSON TM-T20 Receipt");
            $connector = new WindowsPrintConnector("smb://DESKTOP-KV6GLE9/EPSON TM-T20 Receipt");


            /* Print a "Hello world" receipt" */
            $printer = new Printer($connector);

            $printer -> initialize();
            $printer -> setFont(1);
            $printer -> setLineSpacing(10);
            $printer -> setJustification(0);
            $printer -> selectCharacterTable(3);

            // Converter para UTF-8, se necessário
            if (mb_detect_encoding($body, 'UTF-8', true) === false) {
                $body = mb_convert_encoding($body, 'UTF-8');
            }

            $printer -> text($body);
            $printer ->feed(2);
            $printer -> cut();

            /* Close printer */
            $printer -> close();

        } catch (\Exception $e) {
            // echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
            $this->emit("message", "Error. " .  $e -> getMessage() , IconConstants::ICON_ERROR,IconConstants::COLOR_RED);

        }
    }

    /***
     * Função para akustar os espaços nos itens da venda para impressão
    */
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

	/**
     * Retorna a url da imagem
	*/
    public function getImageUrl($item)
    {
        //$url = 'http://127.0.0.1/'.env('URL_IMAGE');
        // Obter o protocolo (HTTP ou HTTPS)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        // Obter o nome do host (domínio)
        $host = $_SERVER['HTTP_HOST'];

        $url = $protocol.'/'. $host.'/'. config('app.url_image');

        if($item->imagem){
            return $url.'/public/storage/'.$item->imagem;
        }else{
            return $url.'/public/storage/produtos/not-image.png';
        }
    }

    /**
     * Retorna o ID do usuário logado
    */
    public function userId(){
        $this->userId = Auth::guard('customer')->id();
    }

    /**
     * Retorna o Id da loja do usuário
    */
    public function lojaId(){
        $this->lojaId = Auth::guard('customer')->user()->loja_id;
    }

    /**
     * Formata o CPF
     */
    private function formatarCpf($cpf)
    {
        // Remove qualquer caracter que não seja número
        $cpfLimpo = preg_replace('/\D/', '', $cpf);

        // Formata o CPF
        $cpfFormatado = substr($cpfLimpo, 0, 3) . '.' .
            substr($cpfLimpo, 3, 3) . '.' .
            substr($cpfLimpo, 6, 3) . '-' .
            substr($cpfLimpo, 9, 2);

        return $cpfFormatado;
    }

    /**
     * Esconde alguns digitos do Cpf
     */
    private function getMaskedCpf($cpf)
    {
        // Esconder os primeiros 7 dígitos do CPF e mostrar apenas os últimos 4
        return substr($cpf, 0, 3) . '.***.***-' . substr($cpf, -2);
    }

    /**
     * Esconde alguns digitos do telefone
    */
    private function getMaskedPhone($phone)
    {
        // Esconder os primeiros dígitos do telefone, mostrar apenas os últimos 4
        $length = strlen($phone);

        if ($length > 4) {
            return str_repeat('*', $length - 4) . substr($phone, -4);
        }

        return $phone; // Retorna o telefone original se tiver 4 dígitos ou menos
    }
}
