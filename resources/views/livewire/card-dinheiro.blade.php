<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="card mb-2 p-0 ml-0" style="display: none" id="card_dinheiro" >
        <div class="card-header bg-primary text-white text-center">
            Valor Dinheiro
        </div>
        <div class="card-body text-monospace">
            <input type="text" name="dinheiro" id="dinheiro" class="form-control form-control-sm" data-id="{{$formaPagamentoId}}" wire:ignore
                   placeholder="Valor Dinheiro" aria-label="Valor Dinheiro" aria-describedby="Valor Dinheiro"
                   data-prefix="R$ " data-thousands="." data-decimal=","/>
        </div>
    </div>
</div>
