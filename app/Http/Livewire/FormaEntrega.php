<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Models\FormaEntrega as Forma;

class FormaEntrega extends Component
{
    public $items;
    public $showEntrega = false;
    public $selectedItemForma;
    public $hasCashback =false;  // Estado inicial do switch


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
        $slug = null;
        $id = null;

        $data = $this->items->firstWhere('id', $value) ?? null;
        if ($data){
             $this->emitTo('sale','vendaUpdated', $data->slug ,$data->id );
        }else{
            $this->emitTo('sale','vendaUpdated', $slug ,$id );
        }
        /***
         * Emite o evento para alterar o total
         */
        $this->emitTo('total-sale','totalSaleVendaUpdated',$slug,$this->hasCashback);
    }

    public function resetSelect(){
        $this->selectedItemForma = null;
    }

    public function render()
    {
        return view('livewire.forma-entrega');
    }
}
