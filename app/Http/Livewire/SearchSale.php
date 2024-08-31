<?php

namespace App\Http\Livewire;

use App\Http\Models\Carts;
use App\Http\Models\Cliente;
use App\Traits\CartTrait;
use http\Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;



class SearchSale extends Component
{
    use CartTrait;
    public $searchSale;

//    protected $listeners = [
//        'searchSale'  =>  'searchSale'
//    ];

    protected $rules = [
        'searchSale' => 'required|digits_between:5,10'
    ];

    protected $messages = [
        'searchSale.required' => 'O campo código venda é obrigatório.',
        'searchSale.max' => 'O campo código venda não pode ter mais que 10 caracteres.'
    ];

    public function mount()
    {

    }

    /**
     * Busca informações da venda pelo código
     *
     */
    public function searchSale()
    {
        try {
            $data = $this->validate($this->rules);



        } catch (ValidationException $e) {
            session()->flash('error',$e->validator->errors()->first('searchSale'));
            $this->emit('focusInputSaleSearch');

        } catch (Exception $e) {
            session()->flash('error',$e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.search-sale');
    }
}
