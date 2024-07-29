<?php
// CartComponent.php

namespace App\Http\Livewire;

use App\Constants\IconConstants;
use App\Http\Models\Carts;
use App\Http\Models\ProdutoVariacao;
use App\Http\Models\VendaProdutos;
use App\Http\Models\Vendas;
use App\Traits\CartTrait;
use Brick\Math\BigDecimal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use NumberFormatter;

class CartComponent extends Component {

    use CartTrait;
    public $searchTerm;
    public $products = [];
    public $cartItems = [];
    public $userId;
    public $barcode;
    public $frete = 0;
    public $codeSale;
    public $selectedItemFormaPgto;
    public $totalItens = 0;
    public $discount = 0;
    public $total=0;

    protected $listeners = ['atualizarCarrinho' => 'render',
                            'addToCart' => 'addToCart',
                            'removerCliente' => 'removerCliente'];

    public function mount()
    {
        $this->userId = Auth::id();
        $this->loadCartItems();
        $this->codeSale = $this->getCodeSaleKN();
        $this->totalItens($this->cartItems);
    }

    public function decrementQuantity(int $product_id, $cant = 1)
    {
        $this->decreaseQuantity($product_id, $cant);
    }

    public function incrementQuantity(int $product_id, $cant = 1)
    {
        //dd($product_id);
        $this->IncreaseQuantity($product_id, $cant);
    }

    public function search(Request $request)
    {
        $this->searchTerm = $request->input('term');

        $this->products = ProdutoVariacao::with('images')->where(function($query) {
            $query->where('variacao', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('subcodigo', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('loja_produtos_new.descricao', 'like', '%' . $this->searchTerm . '%');
        })->where('quantidade', '>', 0)
            ->join('loja_produtos_new', 'loja_produtos_new.id', '=', 'loja_produtos_variacao.products_id')
            ->select('loja_produtos_variacao.*', 'loja_produtos_new.descricao as produto_descricao',  'loja_produtos_new.categoria_id', 'loja_produtos_new.fornecedor_id')
            ->where('loja_produtos_variacao.status',true)
            ->orderBy('variacao', 'asc')->take(10)->get();

        return response()->json($this->products);
    }

    public function addToCart($barcode,$productVariationId)
    {
        //dd($barcode,$productVariationId);
        //$cartItem = Carts::where('product_id', $productId)->first();
        $cartItem = Carts::with('clientes')
            ->where('user_id', Auth::id())
            ->where('produto_variation_id', $productVariationId)
            ->where('status' , 'ABERTO')->first();

        if ($cartItem) {
            $cartItem->increment('quantidade');
        } else {
            $cliente_id = null;
            //verifica se tem venda aberta e se tem cliente associado de ao menos 1 item no carrinho
            $cartItemCliente = Carts::with('clientes')
                ->where('user_id', Auth::id())
                ->where('status' , 'ABERTO')->first();

            //dd($cartItemCliente);
            if ($cartItemCliente && $cartItemCliente->clientes->isNotEmpty()) {
                $cliente_id = $cartItemCliente->clientes[0]->id;
            }

            //busa o produto para inserir no carrinho pelo seu codigo
            $cartItem = ProdutoVariacao::with('images','produtos')
                ->where("subcodigo",$barcode)
                ->where("status",true)
                ->first();

            $carts=[
                'user_id' => $this->userId,
                'produto_variation_id' => $cartItem->id,
                'name' => $cartItem['produtos'][0]->descricao ." - " . $cartItem->variacao,
                'price' => $cartItem->valor_varejo,
                'codigo_produto' => $cartItem->subcodigo,
                'quantidade' => 1,
                'imagem' => count($cartItem->images) > 0 ? $cartItem->images[0]->path : "",
                'cliente_id' =>$cliente_id
            ];

            Carts::create($carts);
        }

        $this->loadCartItems();
        $this->emit("message", "Item adicionado com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
        $this->emit('atualizarCarrinho');
        $this->barcode = "";
        $this->totalItens($this->cartItems);
        $this->discount($this->cartItems);
    }

    public function removeFromCart($cartItemId)
    {
        $cartItem = Carts::find($cartItemId)->delete();;

        $this->barcode = "";
        $this->loadCartItems();
        $this->emit("message", "Item removido com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN);
        $this->totalItens($this->cartItems);
        $this->discount($this->cartItems);

        if(count($this->cartItems) === 0)
            $this->emit('refresh', true);
    }

    /**
     * @param $data
     */
    public function removerCliente($data){
        //dd([$data['user_id'],$data['cliente_id']]);
        $this->removeClientCartTrait($data['user_id'], $data['cliente_id']);
        $this->emit("message", "Cliente removido da venda, com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_RED);
        $this->emit('refresh',true);
    }


    public function render()
    {
        return view('livewire.cart')->extends('layouts.theme.app2')->section('content');
       // return view('livewire.cart')->extends('layouts.theme.app')->section('content');
    }
}
