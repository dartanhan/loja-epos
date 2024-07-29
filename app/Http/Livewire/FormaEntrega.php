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

        $alias = $this->items->firstWhere('id', $value)->alias ?? null;

        if ($alias == 'motoboy-loja') {
            $this->emitTo('sale','vendaUpdated', 'motoboy-loja');
        } else {
            $this->emitTo('sale','vendaUpdated', '');
            $this->selectedItemForma = null;
        }
    }

    public function resetSelect(){
        $this->selectedItemForma = null;
    }

    public function render()
    {
        return view('livewire.forma-entrega');
    }
}
