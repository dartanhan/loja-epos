<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <style>
        /* Estilos customizados podem ser adicionados aqui */
        .item-list {
        max-height: 300px; /* Altura máxima para o div com scroll */
        overflow-y: auto; /* Adiciona scroll vertical se necessário */
        }
    </style>

    <div class="container p-0">
        <div class="row">
            <!--Tipo de Venda -->
            <div class="col-md-4 p-0" style="width: 180px">
                <div class="card mb-2 p-0 ml-0">
                    @livewire('tipo-venda')
                </div>
            </div>

            <!-- Forma de pagamento -->
            <div class="p-0 ml-0" style="flex: 0 0 auto;width: 28%;" wire:ignore>
                <div class="card mb-2 p-0 ml-0">
                    <div class="card-header text-monospace text-center bg-primary text-white">
                        Forma de Pagamento
                    </div>
                    <div class="card-body text-monospace p-2">
                        <select wire:model="selectedItemFormaPgto" class="form-select mb-2 p-1" id="formaPgto">
                            <option value="">Selecione?</option>
                            @foreach($paymentMethods as $payment)
                                <option value="{{ $payment->id }}" data-slug="{{$payment->slug}}">{{ $payment->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2 p-0 ml-0" style="width: 180px;display: none" id="card_dinheiro" wire:ignore>
                <div class="card mb-2 mb-2 p-0 ml-0">
                    <div class="card-header bg-primary text-white text-center">
                        Valor Dinheiro
                    </div>
                    <div class="card-body text-monospace">
                        <input type="text" name="dinheiro" id="dinheiro" class="form-control" wire:ignore
                               placeholder="Valor Dinheiro" aria-label="Valor Dinheiro" aria-describedby="Valor Dinheiro"
                               wire:model="valorRecebido" data-prefix="R$ " data-thousands="." data-decimal=","/>
                    </div>
                </div>
            </div>
            <!-- Forma de entrega -->
            <div class="col-md-3 p-0 ml-0" >
                   @livewire('forma-entrega')
            </div>
        </div>
        <!-- Lista de produtos com imagens -->
        <div class="row">
            <div class="card p-0 ml-0">
                <div class="card-header bg-primary text-white">
                    Itens da Venda
                </div>
                <div class="card-body item-list">
                    <table class="table table-hover table-striped table-responsive">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th class="" colspan="2">Descrição</th>
                                <th class="">Valor</th>
                                <th class="">Qtd</th>
                                <th class="">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                @if(!empty($item->imagem))
                                    <?php
                                    /** @var TYPE_NAME $item */
                                    $image = asset(env("URL_IMAGE").'/public/storage/'.$item->imagem);
                                    ?>
                                @else
                                    <?php
                                    $image = asset(env('URL_IMAGE') . '/public/storage/produtos/not-image.png');
                                    ?>
                                @endif
                                <tr>
                                    <td><img src="{{$image}}" alt="{{$item->name}}" title="{{$item->name}}" data-toggle="tooltip" data-placement="top" class="product-img rounded"></td>
                                    <td>
                                        <span class="product-name">{{$item->codigo_produto}} - {{$item->name}}</span>
                                    </td>
                                    <td class="product-price">
                                        <span class="product-price">R$ {{number_format($item->price, 2, ",", ".")}}</span>
                                    </td>
                                    <td class="product-quantity">
                                        <span class="product-quantity">({{$item->quantidade}})</span>
                                    </td>
                                    <td class="product-price">
                                        <span class="product-price badge bg-primary rounded-pill">R$ {{number_format($item->price*$item->quantidade, 2, ",", ".")}}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Total da venda -->
        <div class="row">
            <div class="card mb-3 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Frete
                </div>
                <div class="card-body">
                    <div class="total-container text-center">
                        <span class="total-value d-block">R$ {{number_format($frete ,2,",",".")}}</span>
                    </div>
                </div>
            </div>

            <div class="card mb-3 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Troco
                </div>
                <div class="card-body">
                    <div class="total-container text-center">
                        <span class="total-value d-block">R$ {{number_format($troco,2,",",".")}}</span>
                    </div>
                </div>
            </div>
            <div class="card mb-3 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Cashback
                </div>
                <div class="card-body">
                    <div class="total-container text-center">
                        <span class="total-value d-block">R$ {{number_format($cashback,2,",",".")}}</span>
                    </div>
                </div>
            </div>
            <div class="card mb-3 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Descontos
                </div>
                <div class="card-body">
                    <div class="total-container text-center">
                        <span class="total-value d-block">R$ {{number_format($discount,2,",",".")}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card mb-3 p-0 ml-0">
                <div class="card-header bg-success text-white text-center">
                    <h4>TOTAL GERAL</h4>
                </div>
                <div class="card-body">
                    <div class="total-container text-center">
                        <span class="total-value total-card d-block">R$ {{number_format($total,2,",",".")}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
