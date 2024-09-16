<div xmlns:wire="http://www.w3.org/1999/xhtml">
        <div class="d-flex justify-content-between mb-2">
            <span><b>Total Itens</b></span>
            <span class="valorTotais">{{ $totalItens }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span><b>Subtotal</b></span>
            <span class="valorTotais">R$ {{ number_format($subTotal, 2,",",".") }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span><b>Descontos</b></span>
            <span class="valorTotais">R$ {{ number_format($discount , 2,",",".") }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>
                <b>
                    Cashback  
                        @if($cashback > 0)
                            <span class="cursor" data-toggle="tooltip" data-placement="top" title="Atenção! Cliente possui cashback disponível!">
                                <i class="fa-regular fa-circle-question fa-fade"></i>
                            </span>
                        @endif
                </b>
            </span>
            <span class="valorTotais">R$ {{ number_format($cashback , 2,",",".") }}</span>
        </div>
</div>
