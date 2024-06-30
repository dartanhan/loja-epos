<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Models\FormaEntrega as Forma;

class FormaEntrega extends Component
{
    public $items;
    public $showEntrega = false;
   

    protected $listeners = ['tipoVendaUpdated' => 'handleTipoVendaUpdated'];

    public function mount(){
        $this->items = Forma::where("status", true)->orderby("descricao","asc")->get();
    }

    public function handleTipoVendaUpdated($tipoVenda)
    {
        $this->showEntrega = ($tipoVenda == 'online');
    }

    public function render()
    {
        return view('livewire.forma-entrega');
    }
}
