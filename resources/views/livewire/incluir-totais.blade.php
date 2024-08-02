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
            <span><b>Cashback</b></span>
            <span class="valorTotais">R$ {{ number_format($cashback , 2,",",".") }}</span>
        </div>
</div>
