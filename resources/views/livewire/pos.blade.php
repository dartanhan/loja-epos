<div xmlns:livewire="" xmlns:wire="http://www.w3.org/1999/xhtml">

    <!-- button wire:click.prevent="Revocar()" type="button" class="btn btn-dark mbmobile mr-5">Revocar Todos</button>

    <button wire:click.prevent="SyncAll()" type="button" class="btn btn-dark mbmobile inblock mr-5">Sincronizar Todos</button -->

    <div class="container mt-2">
        <div class="">
            <div class="row header_pdv">
                <div class="operadora">
                    <p>{{\App\Models\User::first()->sexo === "F" ? "Operadora:" : "Operador:"}}  {{ \App\Models\User::first()->name }}</p>
                </div>
            </div>
            <div class="row p-1">
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    <div class="card mt-2" style="height: 98.9%;">
{{--                        <div class="row p-1">--}}
                            <!-- Div para exibir informações do produto selecionado -->
{{--                            @if($productDetails)--}}
{{--                               @ include('livewire.pos.partials.detail')--}}
{{--                            @endif--}}
{{--                        </div>--}}
                        <livewire:cart-component></livewire:cart-component>
                    </div>
                </div>

                <div class="card col-md-6 mt-2">
                    <input type="hidden" name="codigoHidden" id="codigoHidden"/>
                    <div class="row p-1 relative">
                        <div class="w-75 p-1 border-lable-flt">
                            <input type="text"  name="searchProduct" id="searchProduct"
                               wire:keydown.enter="$emit('addToCart','')"
                               wire:model="barcode" class="form-control custom-disabled" placeholder="Digite o Código/Nome do Produto" autofocus>
                        </div>
                        <div class="w-25 p-1 border-lable-flt">
                            <input type="text" name="codigoVenda" id="codigoVenda"
                                   class="form-control custom-disabled" data-toggle="tooltip" data-placement="top" title="Código da Venda"
                                   placeholder="Código Venda" value="<?php echo Session::get('codigoVenda')?>" readonly>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="w-75 p-1">
                            <input type="text" name="descricao" id="descricao" class="form-control custom-disabled" placeholder="Descrição" readonly>
                        </div>
                        <div class="w-25 p-1">
                            <input type="text" name="codigo" id="codigo" class="form-control custom-disabled" placeholder="Código" readonly>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="w-50 p-1">
                            <div class="imagem" id="imagem"></div>
                        </div>
                        <div class="ml-auto d-flex">
                            <div class="w-20 p-1 ml-auto">
                                <div class="caixa">
                                    <span>Em Estoque</span>
                                </div>
                                <div class="caixa mt-2">
                                    <span>Dinheiro</span>
                                </div>
                                <div class="caixa caixa3 mt-2">
                                    <span>Pix, Débito e Crédito(2x)</span>
                                </div>
                                <div class="caixa caixa mt-2">
                                    <span>Crédito(3x à 6x)</span>
                                </div>
                                <div class="caixa caixa mt-2">
                                    <span>Caixa Fechada</span>
                                </div>
                            </div>
                            <div class="w-20 p-1 ml-auto">
                                <div class="caixa">
                                    <span id="quantidade"></span>
                                </div>
                                <div class="caixa mt-2"><!-- Dinheiro-->
                                    <span id="valor_lista"></span>
                                </div>
                                <div class="caixa mt-2"><!-- PIX, DÉBITO E CRÉDITO(2X)-->
                                    <span id="valor_cartao_pix"></span>
                                </div>
                                <div class="caixa mt-2"><!-- CRÉDITO(3X À 6X)-->
                                    <span id="valor_parcelado"></span>
                                </div>
                                <div class="caixa mt-2"><!-- CAIXA FECHADA-->
                                    <span id="valor_atacado"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <livewire:cart-total></livewire:cart-total>
{{--                    <div class="row d-flex">--}}
{{--                        <div style="width: 100%">--}}
{{--                            <div class="columnTotalDesc md-auto">--}}
{{--                                <p>Total Dinheiro</p>--}}
{{--                                <p>Total Pix, Débito e Crédito(até 2x)</p>--}}
{{--                                <p>Total Crédito (3x à 6x)</p>--}}
{{--                            </div>--}}
{{--                            <div class="column ml-auto">--}}
{{--                                <p>--}}
{{--                                    <?php--}}
{{--                                        /** @var TYPE_NAME $total */--}}
{{--                                        echo "R$ ".number_format($total,2,",",".");--}}
{{--                                    ?>--}}
{{--                                </p>--}}
{{--                                <p>R$ 0,00</p>--}}
{{--                                <p>R$ 0,00</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
            </div>
            <div class="card mt-1" style="margin-left: 4px">
                <div class="row">
                    <div class="col-md-3">
                        <div class="column p-3">
                            <p>Cliente</p>
                            <p>CashBack Disponível</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="column p-3">
                            <p>Karla Neves <i class="fas fa-edit"></i></p>
                            <p>R$ 0,00 <span class="ml-auto"><input type="checkbox" data-toggle="toggle" data-size="xl" data-on="SIM" data-off="NÃO"/></span></p>
                        </div>
                    </div>
                        <div class="col-md-3" style="margin-top: 15px;margin-left: -30px">
                            <div class="column vendadupla divVenda">
                                <p>Venda Dupla</p>
                            </div>
                            <div class="row">
                                <div class="row" style="margin-top: 0px;margin-left: 20px;">
                                    <div class="col-md-6">
                                        <div class="caixa caixablack" style="margin-left: 10px">
                                            <p>Fluxo de Caixa</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="caixa caixablack">
                                            <p>Cancelar Venda</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-3 p-3" >
                            <div class="column divVenda" style="margin-right: -12px">
                                <p>Finalizar Venda</p>
                            </div>
                            <div class="row">
                                <div class="row" style="margin-top: 0px;margin-left: 30px;">
                                    <div class="col-md-6">
                                        <div class="caixa caixablack">
                                            <p>Orçamento</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="caixa caixablack">
                                            <p>Troca</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
            <div class="mt-2"></div>
        </div>
    </div>
    <!-- Botão para abrir o modal (pode estar escondido, pois será acionado pelo evento de teclado) -->
    <button id="botaoAbrirModal" style="display: none;" data-toggle="modal" data-target="#meuModal"></button>
    <!-- Modal -->
    <div class="modal fade" id="meuModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white" id="modalLabel">FECHAR VENDA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modalFecharVenda">
                        <div class="card w-100">
                            <div class="card-header text-center" id="header"></div>
                                <div class="card-body d-flex">
                                    <div class="row w-100 text-center align-items-center">
                                        <div class="col-md-4" id="divDinner">
                                            <!-- Inputs -->
                                            <div class="form-group">
                                                <label for="valorPagar"><strong>Valor a Pagar:</strong></label>
                                                <input type="text" class="form-control cssInput" id="valorPagar">
                                            </div>
                                            <div class="form-group">
                                                <label for="troco"><strong>Troco:</strong></label>
                                                <input type="text" class="form-control cssInput" id="troco" readonly="true">
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="divCartao">
                                            <div class="form-group">
                                                <select id='bandeiraCartao' class='form-select'>
                                                    <option value="0">Selecione a Bandeira</option>
                                                    <option value="1">Visa/MasterCard</option>
                                                    <option value="2">Elo</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select id='formaPgto' class='form-select'></select>
                                            </div>
                                        </div>

                                        <div class="col-md-8 text-center align-items-center mt-2">
                                            <!-- Div em destaque -->
                                            <div class="alert alert-info">
                                                <h4 class="alert-heading">Valor Total:</h4>
                                                <p class="mb-0" id="valorTotalVenda"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary" data-dismiss="modal">Fechar</button>
                    <!-- Outros botões aqui -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal2" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white">FECHANDO A VENDA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Aguarde..</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>
