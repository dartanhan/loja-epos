<?php

namespace App\Http\Livewire;

use App\Enums\StatusVenda;
use App\Http\Models\Carts;
use App\Http\Models\Cliente;
use App\Http\Models\Vendas;
use App\Traits\CartTrait;
use http\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Constants\IconConstants;


class SearchTroca extends Component
{
    use CartTrait;
    public $client;
    public $clienteId=null;
    public $cartItems;
    public $userId;
    public $codeSale; // Declare a propriedade aqui

    protected $listeners = [
        'resetInputFields'  =>  'resetInputFields'
    ];

    protected $rules = [
        'codeSale' => 'required|string|max:11'
    ];

    protected $messages = [
        'codeSale.required' => 'O campo Código Venda é obrigatório.',
        'codeSale.max' => 'O campo Código Venda não pode ter mais que 11 caracteres.',
    ];

    public function mount()
    {
        $this->userId();
    }

    /**
     * Busca o cliente e exibe na modal as informações
     *
     */
    public function searchTroca()
    {
        try {
            //Valida a entrada
            $this->validate($this->rules);

            // Pesquisar A VENDA
            $sales = Vendas::with('products','cliente')->where('codigo_venda',$this->codeSale)->first();

            // Verificar se existe
            if (is_null($sales)) {
                session()->flash('notfound', "Atenção! Venda código {$this->codeSale}, não localizada!");
                $this->emit('focusInputSearch','codeSale');
            }else{
                //fecha o modal caso ache a venda
               // $this->emit('closeModal','openModalBarterSale');

                //dd($sales->id);
               // $carts = Carts::where('venda_id',$sales->id)->update(['status' => StatusVenda::TROCA,'user_id' => $this->userId]);
                if($sales){
                    $this->cartItems = $sales;
                    $this->emit("message", "Venda localizada com sucesso!", IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,false,false);
                }
            }
        } catch (ValidationException $e) {
            session()->flash('error', $e->validator->errors()->first());
            $this->emit('focusInputSearch','codeSale');
        }
    }

    /**
     * Inclui venda no carrinho
    */
    public function associarCliente(){
        //dd('includeClient' , $this->userId , Auth::guard('customer')->id(),  $this->clienteId);

        $carts = Carts::where('user_id', $this->userId)->where("status", "ABERTO")->get();

        // Itera sobre cada carrinho encontrado e atualiza o cliente_id
        foreach ($carts as $cart) {
            $cart->cliente_id = $this->clienteId;
            $cart->save();
        }

        $this->emit('message', 'Cliente adicionado à venda com sucesso!!',IconConstants::ICON_SUCCESS,IconConstants::COLOR_GREEN,true);
        //$this->emit('refresh',true);
        $this->resetInputFields();

    }

    public function resetInputFields()
    {
        $this->codeSale = "";
    }

    public function render()
    {
        return view('livewire.search-troca');
    }

}
