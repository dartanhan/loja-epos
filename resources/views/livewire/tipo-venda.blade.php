<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="card-header text-monospace text-center bg-primary text-white p-2">
        Tipo de Venda
    </div>
    <div class="card-body text-monospace p-2">
        <select wire:model="selectedItem" class="form-select mb-2  p-1" id="tipoVenda">
            <!--option value="">Selecione?</option-->
            @foreach($items as $item)
                <option value="{{$item->id}}" {{ $selectedItem == $item->id ? 'selected' : '' }}>
                    {{$item->descricao}}
                </option>
            @endforeach
        </select>
    </div>

</div>
