<div xmlns:wire="http://www.w3.org/1999/xhtml">
    @if(count($cartItems) > 0)
        <div class="table-responsive flex-grow-1 div-scroll-container">
            <div class="content">
                <table class="table table-hover table-striped text-center table-responsive" style="padding: 2px">
                    <thead class="table-dark">
                    <tr>
                        <th class="" style="width: 650px" colspan="2">DESCRIÇÃO</th>
                        <th class="" style="width: 50px;">DESCONTO</th>
                        <th class="" style="width: 120px">VALOR</th>
                        <th class="" style="width: 90px">QTD</th>
                        <th class="" style="width: 120px">SUBTOTAL</th>
                    </tr>
                    </thead>
                    <tbody >
                    @foreach($cartItems as $item)

                        <tr id="{{$item->id}}" >
                            <td class="text-center">
                                    <button wire:click="removeFromCart({{ $item->id }})"
                                            data-toggle="tooltip" data-placement="top" title="Remover Produto"
                                            class="border-0"  style="cursor: pointer" wire:ignore>
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>
                            </td>
                            <td class="text-left">
                                @if(!empty($item->imagem))
                                    <span class="cart-product-img"
                                          style="background-image: url('{{ $this->getImageUrl($item)  }}'); opacity: 1;"
                                          data-toggle="tooltip" data-placement="top" title="{{ $item->name }}" wire:ignore>
                                                @if($item->quantidade == $item->variations[0]->quantidade)
                                            <span class="cart-product-img-tip">Último Disponível</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="cart-product-img"
                                          style="background-image: url('{{ $this->getImageUrl($item) }}'); opacity: 1;"
                                          data-toggle="tooltip" data-placement="top" title="{{ $item->name }}" wire:ignore>
                                        @if($item->quantidade == $item->variations[0]->quantidade)
                                            <span class="cart-product-img-tip">Último Disponível</span>
                                        @endif
                                    </span>
                                @endif
                                <span class="item-description">{{$item->codigo_produto}} - {{ $item->name}}</span>
                            </td>
                            <td>
                                @if($item->quantidade < 5 && $item->variations[0]->percentage > 0)
                                    <span class="item-description text-red cursor"
                                          data-toggle="tooltip" data-placement="top"
                                          title="Atenção! Produto elegível com desconto da loja, somente para valor no varejo!" wire:ignore>
                                            <i class="fa-regular fa-circle-question fa-fade"></i>
                                            {{$item->variations[0]->percentage}}%

                                    </span>
                                @endif
                            </td>
                            <td>
                                <span>R$ {{number_format($item->price ,2,",",".")}}</span>
                                @if($item->quantidade >= 5)
                                    <br>
                                    <div style="color: #d90a17;font-size: 11px" title="Ítem no Atacado" data-toggle="tooltip">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-index-thumb" viewBox="0 0 16 16">
                                                    <path d="M6.75 1a.75.75 0 0 1 .75.75V8a.5.5 0 0 0 1 0V5.467l.086-.004c.317-.012.637-.008.816.027.134.027.294.096.448.182.077.042.15.147.15.314V8a.5.5 0 0 0 1 0V6.435l.106-.01c.316-.024.584-.01.708.04.118.046.3.207.486.43.081.096.15.19.2.259V8.5a.5.5 0 1 0 1 0v-1h.342a1 1 0 0 1 .995 1.1l-.271 2.715a2.5 2.5 0 0 1-.317.991l-1.395 2.442a.5.5 0 0 1-.434.252H6.118a.5.5 0 0 1-.447-.276l-1.232-2.465-2.512-4.185a.517.517 0 0 1 .809-.631l2.41 2.41A.5.5 0 0 0 6 9.5V1.75A.75.75 0 0 1 6.75 1M8.5 4.466V1.75a1.75 1.75 0 1 0-3.5 0v6.543L3.443 6.736A1.517 1.517 0 0 0 1.07 8.588l2.491 4.153 1.215 2.43A1.5 1.5 0 0 0 6.118 16h6.302a1.5 1.5 0 0 0 1.302-.756l1.395-2.441a3.5 3.5 0 0 0 .444-1.389l.271-2.715a2 2 0 0 0-1.99-2.199h-.581a5 5 0 0 0-.195-.248c-.191-.229-.51-.568-.88-.716-.364-.146-.846-.132-1.158-.108l-.132.012a1.26 1.26 0 0 0-.56-.642 2.6 2.6 0 0 0-.738-.288c-.31-.062-.739-.058-1.05-.046zm2.094 2.025"/>
                                                </svg>
                                                Atacado
                                            </span>
                                        <br>
                                        <span style="color: gray; text-decoration: line-through; margin-left: 15px;">
                                            R$ {{ number_format($item->variations[0]->valor_varejo ,2,",",".") }}
                                        </span>
                                    </div>
                                @endif

                            </td>
                            <td class="text-center">
                                @if($item->quantidade > 1)
                                    <span>
                                        <a href="#" class="text-decoration-none"
                                           wire:click.prevent="decrementQuantity({{ $item->produto_variation_id }})">
                                            <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                        </a>
                                    </span>
                                @endif
                                <span class="col-d-1">{{ $item->quantidade }}</span>
                                @if($item->quantidade < $item->variations[0]->quantidade)
                                    <span>
                                        <a href="#" class="text-decoration-none"
                                           wire:click.prevent="incrementQuantity({{ $item->produto_variation_id }})">
                                            <i class="fa fa-plus-circle " aria-hidden="true"></i>
                                        </a>
                                    </span>
                                @endif
                            </td>
                            <td> R$ {{number_format($item->price * $item->quantidade,2,",",".")}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="d-flex flex-column justify-content-center align-items-center" style="height: 450px;">
{{--            <img src="https://via.placeholder.com/100" alt="Adicionar produtos" class="mb-3" style="width: 100px; height: auto;">--}}
            <h5 class="text-center text-muted">Adicionar produtos para venda</h5>
        </div>

    @endif
</div>
