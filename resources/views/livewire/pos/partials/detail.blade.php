<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="connect-sorting">
        <?php
        $items = \Cart::getContent();
     //   Cart::remove(1);
      //  dd($items)?>
    @if($total > 0)
        <div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
            <table class="table table-bordered table-striped">
                <thead class="text-white" style="background: #3B3F5C">
                <tr>
                    <th width="10%"></th>
                    <th class="table-th text-left text-white">DESCRIÇÃO</th>
                    <th class="table-th text-center text-white">PREÇO</th>
                    <th width="13%" class="table-th text-center text-white">QTD</th>
                    <th class="table-th text-center text-white">TOTAL</th>
                    <th class="table-th text-center text-white">AÇÕES</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td class="text-center table-th">
                            @if(count($item->attributes) > 0)
                                <span>
                                <img src="{{ asset($item->attributes['imagem']) }}" alt="Imagem do Produto" height="90" width="90" class="rounded">
                            </span>
                            @endif
                        </td>
                        <td>
                            <h6>{{$item->name}}</h6>
                        </td>
                        <td class="text-center">R$ {{number_format($item->price,2,",",".")}}</td>
                        <td>
                            <input type="number" id="r{{$item->id}}" wire:change="updateQty({{$item->id}}, $('#r' + {{$item->id}}).val() )" style="font-size: 1rem!important" class="form-control text-center" value="{{$item->quantity}}">
                        </td>
                        <td class="text-center">
                            <h6>
                                R$ {{number_format($item->price * $item->quantity,2,",",".")}}
                            </h6>
                        </td>
                        <td class="text-center">
                            <button onclick="Confirm('{{$item->id}}', 'removeItem', 'CONFIRMA EM REMOVER ESSE ITEM?')" class="btn btn-dark mbmobile">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button wire:click.prevent="decreaseQty({{$item->id}})" class="btn btn-dark mbmobile">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button wire:click.prevent="increaseQty({{$item->id}})" class="btn btn-dark mbmobile">
                                <i class="fas fa-plus"></i>
                            </button>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div>
            <h5 class="text-center text-muted">Adicionar produtos para venda</h5>
{{--            <button wire:click.prevent="$emit('scan-code-byid',1)" class="btn btn-dark">--}}
{{--                <i class="fas fa-cart-arrow-down mr-1"></i>--}}
{{--                AGREGAR--}}
{{--            </button>--}}

{{--            <button wire:click.prevent="$emit('postAdded',1)">TESTE EMIT</button>--}}
        </div>
    @endif
    </div>
</div>
