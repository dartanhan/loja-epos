<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Models\FormaEntrega as Forma;

class FormaEntrega extends Component
{
    public $items;
    public $showEntrega = false;
    public $selectedItemForma;


    protected $listeners = ['tipoVendaUpdated' => 'handleTipoVendaUpdated','formaEntregaResetSelect'=>'resetSelect'];

    public function mount(){
        $this->items = Forma::where("status", true)->orderby("descricao","asc")->get();
    }

    public function handleTipoVendaUpdated($tipoVenda)
    {
        $this->showEntrega = ($tipoVenda == 'online');
    }

    public function updatedSelectedItemForma($value)
    {

        $slug = $this->items->firstWhere('id', $value)->slug ?? null;

        if ($slug == 'motoboy-loja') {
            $this->emitTo('sale','vendaUpdated', 'motoboy-loja');
        } else {
            $this->emitTo('sale','vendaUpdated', '');
        }
        /***
         * Emite o evento para alterar o total
         */
        $this->emitTo('total-sale','totalSaleVendaUpdated',$slug);
    }

    public function resetSelect(){
        $this->selectedItemForma = null;
    }

    public function render()
    {
        return view('livewire.forma-entrega');
    }
}
