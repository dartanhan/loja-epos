<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <select class="form-select-sm chosen-tipo-troca" name="selTipoTroca" id="selTipoTroca" style="width: 140px" wire:ignore>
        <option value="">Selecione</option>
        @foreach($items as $item)
            <option value="{{$item->id}}" data-slug="{{$item->slug}}">
                {{$item->descricao}}
            </option>
        @endforeach
    </select>
</div>
