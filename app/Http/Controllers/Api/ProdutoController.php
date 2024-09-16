<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\ProdutoVariacao;
use App\Models\LojaVenda;
use App\Models\VendaProdutos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProdutoController extends Controller
{

    protected $vendas, $vendasProdutos;

    public function __construct(VendaProdutos $vendasProdutos, LojaVenda $vendas)
    {
        $this->vendasPodutos = $vendasProdutos;
        $this->vendas = $vendas;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
           // dd($request->header("json"));
            $data = json_decode($request->header("json"));

            //Salvo a venda
            $sale = $this->vendas->create(["codigo_venda" =>  $data->codigo_venda]);

            if($sale->exists){
                $total = count($data->produtoModel);

                for ($i =0; $i < $total; $i++) {
                    $this->vendasProdutos = new VendaProdutos();
                    $this->vendasProdutos->venda_id = $sale->id;
                    $this->vendasProdutos->codigo_produto = $data->produtoModel[$i]->codigo_produto;
                    $this->vendasProdutos->descricao = $data->produtoModel[$i]->descricao;
                    $this->vendasProdutos->valor_produto = $data->produtoModel[$i]->valor_produto;
                    $this->vendasProdutos->quantidade = $data->produtoModel[$i]->quantidade;
                    $this->vendasProdutos->save();
                }
            }

        }catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => $e->getMessage()), 500);
        }
        return Response::json(array('success' => true), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $products = ProdutoVariacao::where('codigo_produto', $id)->first();

            if(!$products) {
                $products['success'] = false;
                $products['message'] = "ProdutoVariacao não localizado!";
                return Response::json($products);
            }
        } catch (\Exception $e) {
            return Response::json(array('success' => false, 'message' => $e), 500);
        }
        $products['quantidade'] = 1;
        $products['success'] = true;
        return Response::json($products);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
