<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <style>
        /* Estilos customizados podem ser adicionados aqui */
        .item-list {
        max-height: 250px; /* Altura máxima para o div com scroll */
        overflow-y: auto; /* Adiciona scroll vertical se necessário */
        }
        #container > div {
            flex: 1; /* Distribui o espaço igualmente */
        }
    </style>

    <div class="container p-0">
        <div class="row" style="display: flex; gap: 10px; /* Espaço entre as divs */">
            <!--Tipo de Venda -->
            <div class="col-md-4 p-0" style="width: 180px">
                <div class="card mb-3 p-0 ml-0">
                    @livewire('tipo-venda')
                </div>
            </div>

            <!-- Forma de pagamento -->
            <div class="p-0 ml-0" style="flex: 0 0 auto;width: 32%;" wire:ignore>
                <div class="card p-0 ml-0">
                    <div class="card-header text-monospace text-center bg-primary text-white">
                        Forma de Pagamento
                    </div>
                    <div class="card-body text-monospace p-2 mb-1 ml-0">
                        <select  class="form-select chosen-select" id="formaPgto" multiple data-placeholder="Selecione forma de Pagamento">
                            @foreach($paymentMethods as $payment)
                                <option value="{{ $payment->id }}" data-slug="{{$payment->slug}}" data-text="{{ $payment->nome }}">{{ $payment->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!-- Input de venda dupla-->
            <div class="col-md-5 p-0 ml-0" wire:ignore>
                <div class="card mb-2 mb-2 p-0 ml-0" style="display: none" id="card_venda_dupla" wire:ignore>
                    <div class="card-header bg-primary text-white text-center" >
                        Venda em Pagamento Duplo
                    </div>
                    <div class="card-body text-monospace">
                        <div id="paymentInputsContainer"></div>
                    </div>
                </div>
            </div>
            <!-- Forma de entrega -->
            <div class="col-md-3 p-0 ml-0" >
                @livewire('forma-entrega')
            </div>
            <!-- Input de dinheiro-->
            <div class="col-md-2 p-0 ml-0" style="width: 180px;">
                @livewire('card-dinheiro')
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
                                <tr>
                                    <td class="p-1" style="width: 120px;">
{{--                                        <img src="{{$this->getImageUrl($item)}}"--}}
{{--                                             alt="{{$item->name}}"--}}
{{--                                             title="{{$item->name}}" data-toggle="tooltip" data-placement="top"--}}
{{--                                             class="product-img rounded " wire:ignore/>--}}

                                        @if(!empty($item->imagem))
                                            <span class="cart-product-img cart-product-img-view"
                                                  style="background-image: url('{{ $this->getImageUrl($item)  }}'); opacity: 1;"
                                                  data-toggle="tooltip" data-placement="top" title="{{ $item->name }}">
                                                @if($item->quantidade == $item->variations[0]->quantidade)
                                                    <span class="cart-product-img-tip">Último Disponível</span>
                                                @endif
                                             </span>
                                        @else
                                            <span class="cart-product-img cart-product-img-view "
                                                  style="background-image: url('{{ $this->getImageUrl($item) }}'); opacity: 1;"
                                                  data-toggle="tooltip" data-placement="top" title="{{ $item->name }}">
                                                @if($item->quantidade == $item->variations[0]->quantidade)
                                                    <span class="cart-product-img-tip">Último Disponível</span>
                                                @endif
                                        @endif
                                            </span>
                                    </td>
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
            <div class="card mb-2 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Frete
                </div>
                <div class="card-body">
                    <div class="total-container text-center p-2">
                        <span class="total-value d-block">R$ {{number_format($frete ,2,",",".")}}</span>
                    </div>
                </div>
            </div>

            <div class="card mb-2 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Troco
                </div>
                <div class="card-body">
                    <div class="total-container text-center p-2">
                        <span class="total-value d-block {{$css}}">R$ {{number_format($troco,2,",",".")}}</span>
                    </div>
                </div>
            </div>
            <div class="card mb-2 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Cashback
                </div>
                <div class="card-body">
                    <div class="total-container text-center">
                        <span class="total-value d-block">R$ {{number_format($cashback,2,",",".")}}</span>
                    </div>
                    @if($this->cashback > 0 )
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" wire:click="toggleCashback" {{ $hasCashback ? 'checked' : '' }}>
                            <label class="form-check-label" for="flexSwitchCheckDefault">Usar Cashback</label>
                        </div>
                    @endif
                </div>

            </div>
            <div class="card mb-2 p-0 ml-0">
                <div class="card-header bg-primary text-white text-center">
                    Descontos
                </div>
                <div class="card-body">
                    <div class="total-container text-center p-2">
                        <span class="total-value d-block">R$ {{number_format($discount,2,",",".")}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="p-1" style="display: none" id="divMsgValorNegativo">
                <span class="alert alert-danger d-block mt-2 text-center">
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.93 4.577a.5.5 0 0 0-1.853.034L7 6.077v3.905l.076.355a.5.5 0 0 0 .922 0L8 10.77V6.076l-.07-.285zM8 13.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    </svg>
                    <b>Atenção!</b> Venda não pode ser finalizada com valor negativo!
                </span>
            </div>
            <div class="card mb-2 p-0 ml-0">
                <div class="card-header bg-success text-white text-center p-1">
                    <span class="total-text">TOTAL GERAL</span>
                </div>
                <div class="card-body p-0">
                    <div class="total-container text-center">
                        <span class="total-value total-card d-block">R$ {{number_format($total,2,",",".")}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
