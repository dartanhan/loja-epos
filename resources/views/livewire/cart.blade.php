<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="connect-sorting">
        <?php
       // Cart::cleared($cart);
      // Cart::clear();
      //  Session::put('codigoVenda', null);
        $items = Cart::getContent();

        //dd($cartItems);
         //  Cart::remove(29);
       //   dd($items)
        ?>

        @if(count($items) > 0)
            <div class="table-responsive" style="max-height: 650px;background: #FFFFFF;overflow: auto">
                <table class="table table-bordered table-responsive-sm"  id="tableCart">
                    <thead class="text-white" style="background: #3B3F5C">
                    <tr>
                        <th class="table-th text-left text-white" colspan="2">DESCRIÇÃO</th>
                        <th class="table-th text-center text-white">PREÇO</th>
                        <th width="" class="table-th text-center text-white">QTD</th>
                        <th class="table-th text-center text-white">TOTAL</th>
                        <th class="table-th text-center text-white">AÇÕES</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($items as $item)
                        <tr id="{{$item->id}}">
                            <td colspan="2" style="width: 450px">
                                {{$item->name}}
                            </td>
                            <td class="text-center" style="width: 120px">R$ {{number_format($item->price,2,",",".")}}</td>
                            <td style="width: auto">
                                    <input type="number" id="r{{$item->id}}"
                                        wire:change="updateQty({{$item->id}},
                                        $('#r' + {{$item->id}}).val() , {{$item->quantity}})"
                                       style="width: 50px;height: 30px;padding: 0" class="form-control text-center" value="{{$item->quantity}}">
                            </td>
                            <td class="text-center" style="width: 120px">
                                    R$ {{number_format($item->price * $item->quantity,2,",",".")}}
                            </td>
                            <td class="text-center" style="width: 40px;cursor: pointer">
                                <i class="fas fa-trash-alt text-danger"
                                   data-toggle="tooltip" data-placement="top" title="Remover Produto"
                                   onclick="Confirm('{{$item->id}}', 'removeItem', 'CONFIRMA EM REMOVER ESSE ITEM?')"
                                ></i>

{{--                                <button  class="btn btn-dark btn-sm "  data-toggle="tooltip" data-placement="top" title="Tooltip on top">--}}
{{--                                    <i class="fas fa-minus" wire:click.prevent="decreaseQty({{$item->id}})"></i>--}}
{{--                                </button>--}}
{{--                                <button wire:click.prevent="increaseQty({{$item->id}})" class="btn btn-dark btn-sm ">--}}
{{--                                    <i class="fas fa-plus"></i>--}}
{{--                                </button>--}}

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
