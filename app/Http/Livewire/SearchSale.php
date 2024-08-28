<?php

namespace App\Http\Livewire;

use App\Http\Models\Carts;
use App\Http\Models\Cliente;
use App\Traits\CartTrait;
use http\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Constants\IconConstants;


class SearchSale extends Component
{
    use CartTrait;
    public $codeSale;

    protected $listeners = [
        'searchSale'  =>  'searchSale'
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

           dd($this->codeSale);

          
        } catch (Exception $e) {
            dd($e);
        }
    }

  
    public function render()
    {
        return view('livewire.search-sale');
    }
}
