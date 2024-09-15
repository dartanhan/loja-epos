<?php

namespace App\Http\Livewire;

use App\Http\Models\TipoTroca as ModelTroca;
use Livewire\Component;

class TipoTroca extends Component
{
    public $items;
    public $selectedItem;

   // protected $listeners = ['tipoUpdated' => 'updatedSelectedItem'];

    public function mount(){
        $this->items = ModelTroca::orderby("descricao","asc")->get();
    }


    public function render()
    {
        return view('livewire.tipo-troca');
    }
}
