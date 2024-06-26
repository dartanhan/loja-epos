<div>
    <div class="card-header text-monospace text-center bg-primary text-white">
        Tipo de Venda
    </div>
    <div class="card-body text-monospace">
        <select wire:model="selectedItem" class="form-select mb-3">
            <option selected>Selecione uma opção ?</option>
            @foreach($items as $item)
                <option value="{{$item->id}}" {{ $selectedItem == $item->id ? 'selected' : '' }}>
                    {{$item->descricao}}
                </option>
            @endforeach
        </select>
    </div>

</div>
