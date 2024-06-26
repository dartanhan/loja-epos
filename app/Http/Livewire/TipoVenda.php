<?php

namespace App\Http\Livewire;

use App\Http\Models\TipoVendas;
use Livewire\Component;

class TipoVenda extends Component
{
    public $items;
    public $selectedItem;

    public function mount(){
        $this->items = TipoVendas::orderby("descricao","asc")->get();

        // Definir o item "presencial" como padrão
        $this->selectedItem = $this->items->firstWhere('descricao', 'Presencial')->id ?? null;
    }

    public function updatedSelectedItem($value)
    {
        $descricao = $this->items->firstWhere('id', $value)->descricao ?? null;

        if ($descricao == 'Online') {
            $this->emit('tipoVendaUpdated', 'online');
        } else {
            $this->emit('tipoVendaUpdated', 'presencial');
        }
    }

    public function render()
    {
        return view('livewire.tipo-venda');
    }
}
