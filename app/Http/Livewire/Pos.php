<?php

namespace App\Http\Livewire;

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
    public $products;
    public $total, $itemsQuantity, $change,$userId,$barcode;

    public function mount(){
       $this->userId = Auth::id();
    }

    public function render()
    {
       Cart::session($this->userId);

       return view('livewire.pos')
            ->extends('layouts.theme.app')
            ->section('content');
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
            })->join('loja_produtos_new', 'loja_produtos_new.id', '=', 'loja_produtos_variacao.products_id')
                ->select('loja_produtos_variacao.*', 'loja_produtos_new.descricao as produto_descricao',  'loja_produtos_new.categoria_id', 'loja_produtos_new.fornecedor_id')
                ->where('loja_produtos_variacao.status',1)
                ->orderBy('variacao', 'asc')->take(10)->get();

        return response()->json($this->products);
    }

// escuchar eventos
    protected $listeners = [
        'scan-code'  =>  'ScanCode',
        //'removeItem' => 'removeItem',
        'clearCart'  => 'clearCart',
        //'saveSale'   => 'saveSale',
        'refresh' => '$refresh',
        'scan-code-byid' => 'ScanCodeById'
    ];

}
