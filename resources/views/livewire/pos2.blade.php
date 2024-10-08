<div xmlns:livewire="" xmlns:wire="http://www.w3.org/1999/xhtml">

    <div class="container">
        <!-- Card para Pesquisa de Produtos -->
        <div class="card-container">
            <div class="card search-card d-flex flex-column" style="flex: 3;">
                <div class="card-body">
                    <div class="search-input">
                        <input type="text"  name="searchProduct" id="searchProduct"
{{--                               wire:keydown.enter="$emit('scan-ok','')"--}}
                               wire:model="barcode" class="form-control custom-disabled" placeholder="Pesquisar produtos..." autofocus>
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>
            <div class="search-card-cliente d-flex flex-column" style="flex: 1;">
{{--                  <livewire:incluir-cliente></livewire:incluir-cliente>--}}
                <div class="card d-flex flex-column" >
                    <div class="card-header text-center text-monospace bg-primary text-white">
                        <h5>Cliente</h5>
                    </div>
                    <div class="card-body text-center">
                        @if ($clienteName)
                            <span class="d-inline cliente-associado"  title="Clique para Alterar o Cliente" data-toggle="tooltip" >
                                <h5 class="d-inline" id="openModalBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-heart-eyes" viewBox="0 0 16 16">
                                      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                      <path d="M11.315 10.014a.5.5 0 0 1 .548.736A4.5 4.5 0 0 1 7.965 13a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .548-.736h.005l.017.005.067.015.252.055c.215.046.515.108.857.169.693.124 1.522.242 2.152.242s1.46-.118 2.152-.242a27 27 0 0 0 1.109-.224l.067-.015.017-.004.005-.002zM4.756 4.566c.763-1.424 4.02-.12.952 3.434-4.496-1.596-2.35-4.298-.952-3.434m6.488 0c1.398-.864 3.544 1.838-.952 3.434-3.067-3.554.19-4.858.952-3.434"/>
                                    </svg> {{ $clienteName }}
                                </h5>
                            </span>
                        @else
                            <span class="d-inline" title="Incluir Cliente na Venda" data-toggle="tooltip">
                                <button type="button" class="btn btn-primary btn-sm" id="openModalBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-heart" viewBox="0 0 16 16">
                                      <path d="M9 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h10s1 0 1-1-1-4-6-4-6 3-6 4m13.5-8.09c1.387-1.425 4.855 1.07 0 4.277-4.854-3.207-1.387-5.702 0-4.276Z"/>
                                    </svg> Incluir Cliente
                                </button>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-container d-flex">
            <!-- Card para Itens do Carrinho -->
            <div class="card d-flex flex-column" style="flex: 3;">
                <div class="card-body d-flex flex-column p-0">
                    @if(count($this->items) > 0)
                        <div class="table-responsive flex-grow-1 scroll-container">
                            <div class="content">
                                <table class="table table-hover table-striped text-center table-responsive" style="padding: 2px">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="" style="width: 650px" colspan="2">Descrição</th>
                                            <th class="" style="width: 120px">Valor</th>
                                            <th class="" style="width: 90px">Qtd</th>
                                            <th class="" style="width: 120px">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->items as $key => $item)

                                            <tr id="{{$item->id}}" >
                                                <td class="text-center" style="cursor: pointer">
                                                    <i class="fas fa-trash-alt text-danger"
                                                       data-toggle="tooltip" data-placement="top" title="Remover Produto"
                                                       onclick="Confirma('{{$item->id}}', 'removeItem', 'CONFIRMA EM REMOVER ESSE ITEM?')"></i>
                                                </td>
                                                <td class="text-left">
                                                    @if(!empty($item->imagem))
                                                        <span class="cart-product-img"
                                                              style="background-image: url({{ "http://127.0.0.1/api-loja-new-git/public/storage/".$item->imagem }});
                                                                  opacity: 1;" title="{{ $item->name}}">

                                                            @if($item->quantidade == $item->variations[0]->quantidade)
                                                                <span class="cart-product-img-tip">Último Disponível</span>
                                                            @endif
                                                        </span>
                                                    @else

                                                        <span class="cart-product-img"
                                                              style="background-image: url({{ "https://via.placeholder.com/60"}}); opacity: 1;" title="{{ $item->name}}">

                                                            @if($item->quantidade == $item->variations[0]->quantidade)
                                                                <span class="cart-product-img-tip">Último Disponível</span>
                                                            @endif
                                                        </span>
                                                    @endif
                                                    <span class="item-description">{{ $item->name}}</span>
                                                </td>
                                                <td>
                                                    <span>R$ {{number_format($item->price ,2,",",".")}}</span>
                                                    @if($item->quantidade >= 5)
                                                        <br>
                                                        <div style="color: #d90a17;font-size: 10px" title="Ítem no Atacado" data-toggle="tooltip">
                                                            <span >
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-index-thumb" viewBox="0 0 16 16">
                                                                    <path d="M6.75 1a.75.75 0 0 1 .75.75V8a.5.5 0 0 0 1 0V5.467l.086-.004c.317-.012.637-.008.816.027.134.027.294.096.448.182.077.042.15.147.15.314V8a.5.5 0 0 0 1 0V6.435l.106-.01c.316-.024.584-.01.708.04.118.046.3.207.486.43.081.096.15.19.2.259V8.5a.5.5 0 1 0 1 0v-1h.342a1 1 0 0 1 .995 1.1l-.271 2.715a2.5 2.5 0 0 1-.317.991l-1.395 2.442a.5.5 0 0 1-.434.252H6.118a.5.5 0 0 1-.447-.276l-1.232-2.465-2.512-4.185a.517.517 0 0 1 .809-.631l2.41 2.41A.5.5 0 0 0 6 9.5V1.75A.75.75 0 0 1 6.75 1M8.5 4.466V1.75a1.75 1.75 0 1 0-3.5 0v6.543L3.443 6.736A1.517 1.517 0 0 0 1.07 8.588l2.491 4.153 1.215 2.43A1.5 1.5 0 0 0 6.118 16h6.302a1.5 1.5 0 0 0 1.302-.756l1.395-2.441a3.5 3.5 0 0 0 .444-1.389l.271-2.715a2 2 0 0 0-1.99-2.199h-.581a5 5 0 0 0-.195-.248c-.191-.229-.51-.568-.88-.716-.364-.146-.846-.132-1.158-.108l-.132.012a1.26 1.26 0 0 0-.56-.642 2.6 2.6 0 0 0-.738-.288c-.31-.062-.739-.058-1.05-.046zm2.094 2.025"/>
                                                                </svg>
                                                                Atacado
                                                            </span>
                                                            <br>
                                                            <span style="color: gray; text-decoration: line-through; margin-left: 15px;">
                                                                R$ {{number_format($item->price ,2,",",".")}}
                                                            </span>
                                                        </div>
                                                    @endif

                                                </td>
                                                <td class="text-center">
                                                    @if($item->quantidade > 1)
                                                        <a href="#" title="Remover Item" data-toggle="tooltip" class="text-decoration-none"
                                                           wire:click.defer="decrementQuantity({{ $item->produto_variation_id }})">
                                                            <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                                        </a>
                                                    @else
                                                            <i class="fa fa-minus-circle text-hide" aria-hidden="true"></i>
                                                    @endif
                                                        <span class="col-d-1">{{ $item->quantidade }}</span>
                                                    @if($item->quantidade == $item->variations[0]->quantidade)
                                                            <span title="Estoque máximo atingido" data-toggle="tooltip" class="disabled">
                                                                <i class="fa fa-plus-circle text-hide" aria-hidden="true" ></i>
                                                            </span>
                                                    @else
                                                        <a href="#" title="Adicionar Item" data-toggle="tooltip"  class="text-decoration-none"
                                                           wire:click.defer="incrementQuantity({{ $item->produto_variation_id }})">
                                                            <i class="fa fa-plus-circle " aria-hidden="true"></i>
                                                        </a>
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
                        <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                            <h5 class="text-center text-muted">Adicionar produtos para venda</h5>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card para Totais -->
            <div  style="flex: 1;">
                <div class="card" style="height: 200px">
                    <div class="card-header text-center bg-primary text-white">
                        <h5>Totais</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>R$ {{number_format($subTotal,2,",",".")}}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descontos</span>
                            <span>R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete</span>
                            <span>R$ {{ number_format($taxa,2,",",".")}}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 font-weight-bold">
                            <span>Total</span>
                            <span>R$ {{number_format($total,2,",",".")}}</span>
                        </div>
                    </div>
                </div>
                <div class="card" style="height: 200px;">
                    <div class="card-body">
{{--                        <div class="mb-2 text-center">--}}

{{--                        </div>--}}
                        <div class="mb-2">
                            <label for="frete" class="form-label">CashBack</label>
{{--                            <input type="text" name="cashback" id="cashback" class="form-control" placeholder="Cashback">--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




<!-- Modal -->
<div class="modal fade right" id="slideInModal" tabindex="-1" aria-labelledby="slideInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="slideInModalLabel">Cliente</h5>
                <button type="button" class="btn-close" id="closeModalBtn" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-container d-flex">
                    <div class="card search-card d-flex flex-column" style="flex: 3;">
                        <div class="card-body">
                            <div class="search-input">
                                @livewire('search-client')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/modal.css') }}" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

{{--    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}





</div>
