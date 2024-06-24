<?php

namespace App\Http\Livewire;

use App\Http\Models\Carts;
use App\Http\Models\ProdutoVariacao;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Traits\CartTrait;

class Pos extends Component
{
    use CartTrait;
    public $items = [];
    public $message = 'Hello, Livewire!';
    public $searchTerm;
    public $products,$clienteName;
    public $total,$subTotal,$taxa,$itemsQuantity, $change,$userId,$barcode,$client;

    public function mount(){
       $this->userId = Auth::id();
       $this->loadCartItems();
    }

    public function render()
    {

        return view('livewire.pos2')->extends('layouts.theme.app')->section('content');
    }

    /**
     *Busca os produtos com o JqueryAutoload e exibe na combo o resultado conforme digita
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
        O JS searchProduct emite um livewire para essa função
     *  custom.js
     */
    public function addToCart($barcode){
       //dd($barcode);
        $this->ScanearCode($barcode);
    }

    /**
     * Atualiza a quantidade de item no carrinho
     * @param ProdutoVariacao $product
     * @param int $cant
     * @param $qtdCart
     */
//    public function updateQty(ProdutoVariacao $product, $cant = 1, $qtdCart)
//    {
//        // dd($product , $cant, $qtdCart);
//
//        if ($cant > $qtdCart)
//            // $this->removeItem($product->id);
//            $this->IncreaseQuantity($product,$cant);
//        else
//            // $this->UpdateQuantity($product, $cant);
//            $this->decreaseQuantity($product, $cant);
//
//        //$this->emit('pinta-linha-atacado', null);
//    }

    /***
    * Passsa para o componente CART a solicitação para remover o item
    * @param $productId
    */
    public function removeItem($productId)
    {
        $this->removeCartItem($productId);
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


// escuchar eventos
    protected $listeners = [
        'scan-code'  =>  'ScanCode',
        'removeItem' => 'removeItem',
        'clearCart'  => 'clearCart',
        //'saveSale'   => 'saveSale',
        'refresh' => '$refresh',
        'scan-code-byid' => 'ScanCodeById',
        'addToCart' => 'addToCart',
        'atualizarCarrinho'=>'loadCartItems',
    ];

    public function getTotalCartByUser(){
        return $this->getTotalCartTraitByUser();
    }

  }
