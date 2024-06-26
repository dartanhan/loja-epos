<div>
    <!-- Card de Forma de Entrega (exibido apenas se a venda for "online") -->
    @if ($showEntrega)
        <div class="card mb-3">
            <div class="card-header text-monospace text-center bg-primary text-white">
                Forma de Entrega
            </div>
            <div class="card-body text-monospace">
                <select class="form-select mb-3">
                    <option selected>Selecione uma opção ?</option>
                    @foreach($items as $item)
                        <option value="{{$item->id}}">{{$item->descricao}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
</div>
