<?php

namespace App\Http\Livewire;

use App\Http\Models\Vendas;
use App\Traits\CartTrait;
use http\Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use NumberFormatter;


class SearchSale extends Component
{
    use CartTrait;
    public $searchSale;
    public $reprintSale;
    public $codeSale;
    public $nome;
    public $sale;
    public $cpf;
    public $telefone;
    public $total;
    public $totalPago;

    protected $listeners = [
        'searchSale'  =>  'searchSale'
    ];

    protected $rules = [
        'codeSale' => 'required|string|between:5,10'
    ];

    protected $messages = [
        'codeSale.required' => 'O campo código venda é obrigatório.',
        'codeSale.between' => 'O campo código venda não pode ter mais que 10 caracteres.'
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
            $this->sale = null;
            $data = $this->validate($this->rules);

            $this->codeSale = $data['codeSale'];
            /**
             * Busca a venda e suas relações
            */
            $this->sale = Vendas::with('products', 'cliente','forma_pgto.payments','desconto','cashback','frete')->where('codigo_venda', $this->codeSale)->first();

            if ($this->sale && !empty($this->sale)) {
                $formatter = new NumberFormatter('pt_BR', \NumberFormatter::CURRENCY);
                $this->total =  $formatter->formatCurrency($this->sale->valor_total, 'BRL');

                $desconto = 0;
                if ($this->sale && isset($this->sale->desconto) && count($this->sale->desconto) > 0) {
                    $desconto = $this->sale->desconto[0]->valor_desconto;
                }

                $cashback = 0;
                if ($this->sale && isset($this->sale->cashback) && count($this->sale->cashback) > 0) {
                    $cashback = $this->sale->cashback[0]->valor;
                }

                $this->totalPago = $formatter->formatCurrency($this->sale->valor_total - $desconto - $cashback, 'BRL');

                $this->nome =  'Cliente não identificado';
                if ($this->sale && isset($this->sale->cliente) && count($this->sale->cliente) > 0) {
                    $this->nome = $this->sale->cliente[0]->nome;
                    $this->cpf = $this->getMaskedCpf($this->formatarCpf($this->sale->cliente[0]->cpf));
                    $this->telefone = $this->getMaskedPhone($this->sale->cliente[0]->telefone);
                }
            }else{
                session()->flash('notfound', "Atenção! Venda código {$this->codeSale}, não localizada!");
                $this->emit('focusInputSearch','codeSale');
            }

        } catch (ValidationException $e) {
            session()->flash('error',$e->validator->errors()->first('codeSale'));
            $this->emit('focusInputSearch','codeSale');

        } catch (Exception $e) {
            session()->flash('error',$e->getMessage());
        }
    }

    /**
     * Reimprime a venda
    */
    public function reprintSale(){
        $this->reprintSaleTrait($this->sale);
    }

    public function render()
    {
        return view('livewire.search-sale');
    }
}
