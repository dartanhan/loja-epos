<?php

namespace App\Http\Livewire;

use App\Http\Models\TipoVendas;
use Livewire\Component;

class TipoVenda extends Component
{
    public $items;
    public $selectedItem;

    protected $listeners = ['tipoUpdated' => 'updatedSelectedItem'];

    public function mount(){
        $this->items = TipoVendas::orderby("descricao","asc")->get();

        // Definir o item "presencial" como padrão
        $this->selectedItem = $this->items->firstWhere('slug', 'presencial')->id ?? null;
    }

    /**
     * Ecebe o Id do Tipo da Venda
     * @param $tipoVendaId
     */
    public function updatedSelectedItem($tipoVendaId)
    {
        $alias = $this->items->firstWhere('id', $tipoVendaId)->slug ?? null;


        if ($alias == 'online') {
            $this->emit('tipoVendaUpdated', 'online');
        } else {
            $this->emit('tipoVendaUpdated', 'presencial');
            $this->emitTo('forma-entrega','formaEntregaResetSelect');
            $this->emitTo('sale','vendaUpdated','');
        }
        $this->emitTo('sale','tipoVenda', $tipoVendaId);
    }

    public function render()
    {
        return view('livewire.tipo-venda');
    }
}
