<?php

namespace App\Http\Livewire;

use App\Http\Models\ProdutoVariacao;

use Illuminate\Http\Request;
use Livewire\Component;

class ProductSearch extends Component
{
    public $query;
    public $products;

    public function search(Request $request)
    {
        $this->searchTerm = $request->input('term');
       // $products = Product::where('codigo_produto', 'like', '%' . $searchTerm . '%')->pluck('codigo_produto');

        $products = ProdutoVariacao::with('images')->where(function($query) {
            $query->where('variacao', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('subcodigo', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('loja_produtos_new.descricao', 'like', '%' . $this->searchTerm . '%')
                ->where('loja_produtos_variacao.status',1);
        })->join('loja_produtos_new', 'loja_produtos_new.id', '=', 'loja_produtos_variacao.products_id')
            ->select('loja_produtos_variacao.*', 'loja_produtos_new.descricao as produto_descricao')
            ->orderBy('variacao', 'asc')->take(10)->get();

        return response()->json($products);
    }
}
