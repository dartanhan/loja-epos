<?php
// CartComponent.php

namespace App\Http\Livewire;

use App\Http\Models\Carts;
use App\Http\Models\VendaProdutos;
use App\Http\Models\VendasProdutos;
use App\Http\Models\Vendas;
use App\Http\Models\ProdutoVariacao;
use App\Traits\CartTrait;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class CartComponent extends Component {

    use CartTrait;
    public $searchTerm;
    public $products = [];
    public $cartItems = [];
    public $userId,$barcode;

    protected $listeners = ['atualizarCarrinho' => 'render','addToCart' => 'addToCart'];

    public function mount()
    {
        $this->userId = Auth::id();
        $this->cartItems =  Carts::with(['variations','clientes'])
            ->where('user_id',  $this->userId )
            ->where('status',  'ABERTO' )
            ->get();

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

//    public function updatedSearchTerm()
//    {
//        $this->products = ProdutoVariacao::with('images')->where(function($query) {
//            $query->where('variacao', 'like', '%' . $this->searchTerm . '%')
//                ->orWhere('subcodigo', 'like', '%' . $this->searchTerm . '%')
//                ->orWhere('loja_produtos_new.descricao', 'like', '%' . $this->searchTerm . '%');
//        })->where('quantidade', '>', 0)
//            ->join('loja_produtos_new', 'loja_produtos_new.id', '=', 'loja_produtos_variacao.products_id')
//            ->select('loja_produtos_variacao.*', 'loja_produtos_new.descricao as produto_descricao',  'loja_produtos_new.categoria_id', 'loja_produtos_new.fornecedor_id')
//            ->where('loja_produtos_variacao.status',true)
//            ->orderBy('variacao', 'asc')->take(10)->get();
//
//    }

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
            //dd($cartItem);
            $cartItem = ProdutoVariacao::with('images','produtos')
                ->where("subcodigo",$barcode)
                ->where("status",true)
                ->first();

            $carts=[
                'user_id' => $this->userId,
                'produto_variation_id' => $cartItem->id,
                'name' => $cartItem->subcodigo ." - ".$cartItem['produtos'][0]->descricao ." - " . $cartItem->variacao,
                'price' => $cartItem->valor_varejo,
                'quantidade' => 1,
                'imagem' => count($cartItem->images) > 0 ? $cartItem->images[0]->path : ""
            ];

            Carts::create($carts);
        }

        $this->cartItems = $this->getClientItemCartTrait();

        $this->emit('atualizarCarrinho');
        $this->barcode = "";
    }

    public function removeFromCart($cartItemId)
    {
        $cartItem = Carts::find($cartItemId);

        if ($cartItem->quantidade > 1) {
            $cartItem->decrement('quantidade');
        } else {
            $cartItem->delete();
        }

        $this->barcode = "";
        $this->cartItems = $this->getClientItemCartTrait();
        $this->emit('scan-remove', 'Item removido com sucesso!');
        $this->emit('focus-input-search', null);
        $this->emit('atualizarCarrinho');

        if(count($this->cartItems) === 0)
            $this->emit('refresh', true);
    }

    public function render()
    {
        //return view('livewire.cart');
        return view('livewire.cart')->extends('layouts.theme.app')->section('content');
    }
}
