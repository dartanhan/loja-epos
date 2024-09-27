<?php
// CartComponent.php

namespace App\Http\Livewire;

use App\Constants\IconConstants;
use App\Http\Models\ProdutoVariacao;
use App\Http\Services\ApiService;
use App\Traits\CartTrait;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class CartComponent extends Component {

    use CartTrait;
    protected $apiService;
    public $searchTerm;
    public $products = [];
    public $cartItems = [];
    public $userId;
    public $barcode;
    public $frete = 0;
    public $codeSale;
    public $lojaId;
    public $totalItens = 0;
    public $discount = 0;
    public $total=0;
    public $subTotal=0;
    public $cashback=0;
    public $hasCashback = false;  // Estado inicial do switch
    public $clienteId = 0;

    protected $listeners = ['atualizarCarrinho' => 'render',
                            'addToCart' => 'addToCart',
                            'removerCliente' => 'removerCliente',
                            'cancelSale' => 'cancelSale'];

    public function mount()
    {
        $this->userId();
        $this->loadCartItemsTrait();
        $this->getCodeSaleKN();
        $this->lojaId();
        $this->getClientId();
    }

//    public function search(Request $request)
//    {
//        $this->searchTerm = $request->input('term');
//
//        $this->products = ProdutoVariacao::with('images')->where(function($query) {
//            $query->where('variacao', 'like', '%' . $this->searchTerm . '%')
//                    ->orWhere('subcodigo', 'like', '%' . $this->searchTerm . '%')
//                    ->orWhere('descricao', 'like', '%' . $this->searchTerm . '%')
//                    ->orWhere('gtin', 'like', '%' . $this->searchTerm . '%');
//        })->where('quantidade', '>', 0)
//            ->join('loja_produtos_new as lpn', 'lpn.id', '=', 'loja_produtos_variacao.products_id')
//            ->select('loja_produtos_variacao.*', 'lpn.descricao as produto_descricao',  'lpn.categoria_id', 'lpn.fornecedor_id')
//            ->where('loja_produtos_variacao.status',true)
//            ->orderBy('variacao', 'asc')->take(10)->get();
//
//        return response()->json($this->products);
//    }

    public function search(Request $request)
    {

        $token = (new ApiService)->getToken();

        $response = Http::withToken($token)->get(config('app.url_api_busca'), [
            'term' => $request->input('term')
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Erro ao buscar produtos'], 500);
        }
    }

    /**
     * Adiciona o item ao carrinho
     * @param $barcode
     * @param $productVariationId
     */
    public function addToCart($barcode,$productVariationId)
    {
        $this->addToCartTrait($barcode,$productVariationId);
    }

    /**
     * @param $data
     */
    public function removerCliente($data){
        //dd([$data['user_id'],$data['cliente_id']]);
        $this->removeClientCartTrait($data['user_id'], $data['cliente_id']);
        $this->emit("message", "Cliente removido da venda, com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_RED,true,true);
        //$this->emit('refresh',true);
    }

    /**
     * cancelSale
     * @param $data
     */

    public function cancelSale($data){
        $this->trashCartTrait($data);
    }

    public function render()
    {
        return view('livewire.cart-component')->extends('layouts.theme.app2')->section('content');
       // return view('livewire.cart')->extends('layouts.theme.app')->section('content');
    }
}
