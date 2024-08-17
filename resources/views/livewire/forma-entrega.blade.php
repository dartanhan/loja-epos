<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <!-- Card de Forma de Entrega (exibido apenas se a venda for "online") -->
    @if ($showEntrega)
        <div class="card mb-2 p-0 ml-0">
            <div class="card-header bg-primary text-white text-center p-2">
                Forma de Entrega
            </div>
            <div class="card-body text-monospace" wire:ignore>
                <select wire:model="selectedItemForma" class="form-select mb-1 p-1 chosen-forma-entrega" id="formaEntrega">
                    <option selected>Selecione?</option>
                    @foreach($items as $item)
                        <option value="{{$item->id}}" data-alias="{{$item->slug}}">{{$item->descricao}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
</div>
