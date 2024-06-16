<div xmlns:livewire="" xmlns:wire="http://www.w3.org/1999/xhtml">
    <?php
     //Cart::cleared($cart);
   // Cart::clear();
    //  Session::put('codigoVenda', null);
   // $items = Cart::getContent();
//dd($items);
  //dd($items[0]['attributes']['images']);
    //  Cart::remove(29);
    //   dd($items)
    ?>

    <div class="container">
        <!-- Card para Pesquisa de Produtos -->
        <div class="card-container">
            <div class="card search-card d-flex flex-column" style="flex: 3;">
                <div class="card-body">
                    <div class="search-input">
                        <input type="text"  name="searchProduct" id="searchProduct"
                               wire:keydown.enter="$emit('scan-ok','')"
                               wire:model="barcode" class="form-control custom-disabled" placeholder="Pesquisar produtos..." autofocus>
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
            </div>
            <div class="search-card-cliente d-flex flex-column" style="flex: 1;">
                  <livewire:incluir-cliente-component></livewire:incluir-cliente-component>
            </div>

        </div>
        <div class="card-container d-flex">
            <!-- Card para Itens do Carrinho -->
            <div class="card d-flex flex-column" style="flex: 3;">
{{--                <div class="card-header">--}}
{{--                    <h5>Itens do Carrinho</h5>--}}
{{--                </div>--}}
                <div class="card-body d-flex flex-column p-0">
                    @if(count($items) > 0)
                        <div class="table-responsive flex-grow-1 scrollable-div">
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
                                    @foreach($items as $key => $item)

                                        <tr id="{{$item->id}}" >
                                            <td class="text-center" style="cursor: pointer">
                                                <i class="fas fa-trash-alt text-danger"
                                                   data-toggle="tooltip" data-placement="top" title="Remover Produto"
                                                   onclick="Confirma('{{$item->id}}', 'removeItem', 'CONFIRMA EM REMOVER ESSE ITEM?')"></i>
                                            </td>
                                            <td class="text-left">
                                                @if(!empty($item->imagem))
                                                    <img src="{{ "http://127.0.0.1/api-loja-new-git/public/storage/".$item->imagem }}"
                                                         title="{{ $item->name}}" width="60px" height="60px"/>
                                                @else
                                                    <img src="https://via.placeholder.com/60" title="{{ $item->name}}"/>
                                                @endif
                                                   {{ $item->name}}
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
                                                @if($item->quantidade == $item->variations[0]->quantidade)
                                                    <br>
                                                    <span style="color: gray;font-size: 10px">
                                                       Último Disponível
                                                    </span>
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
                            <span>R$ {{number_format(\App\Traits\CartTrait::getTotalCartByUser(),2,",",".")}}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descontos</span>
                            <span>R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete</span>
                            <span>R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 font-weight-bold">
                            <span>Total</span>
                            <span>R$ {{number_format(\App\Traits\CartTrait::getTotalCartByUser(),2,",",".")}}</span>
                        </div>
                    </div>
                </div>
                <div class="card" style="height: 200px;">
                    <div class="card-body">
                        <div class="mb-2 text-center">

{{--                            @if(!empty($items->clientes) || count($items[0]->clientes) > 0)--}}
{{--                                <label for="cliente" class="form-label">Cliente: </label>--}}
{{--                                <span class="d-inline"><h5 class="d-inline">{{ $items[0]->clientes[0]->nome }}</h5></span>--}}
{{--                            @else--}}
{{--                                <form wire:submit.prevent="searchClient">--}}
{{--                                    <input type="text" name="cliente" id="cliente" wire:model="cpf" maxlength="11"--}}
{{--                                           class="form-control" placeholder="Insira o Cliente">--}}
{{--                                    <button type="submit" class="btn btn-primary">Pesquisar</button>--}}
{{--                                    <!-- Button to Open the Modal -->--}}
{{--                                    <span class="d-inline">--}}
{{--                                        <button type="button" class="btn btn-primary" id="openModalBtn">Incluir Cliente</button>--}}
{{--                                    </span>--}}
{{--                                </form>--}}
{{--                            @endif--}}
                        </div>
                        <div class="mb-2">
                            <label for="frete" class="form-label">Frete</label>
                            <input type="text" name="frete" id="frete" class="form-control" placeholder="Insira o valor do frete">
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
