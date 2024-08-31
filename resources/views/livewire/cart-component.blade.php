<div xmlns:wire="http://www.w3.org/1999/xhtml">
   <!-- resources/views/livewire/cart-component.blade.php -->
    <!--div class="container"-->
    <div>
        <input type="hidden" id="loja_id" name="loja_id" value="{{$lojaId}}">
        <div class="card-container d-flex div-content">
            <!-- Card para Itens do Carrinho -->
            <div class="card d-flex flex-column" style="flex: 4;">
                <div class="card-body d-flex flex-column p-0">
                    @livewire('incluir-cart', key(time()))
                </div>
            </div>

            <!-- Card para Totais -->
            <div  style="flex: 1;">
                <div class="card d-flex flex-column">
                    <div class="card-header text-center bg-primary text-white d-flex align-items-center justify-content-center" wire:ignore>
                        <h6 class="mb-0">Cliente</h6>
                    </div>
                    <div class="card-body text-center">
                        @livewire('incluir-cliente', key(time()))
                    </div>
                </div>
                <div class="card" style="height: auto">
                    <div class="card-header text-center bg-primary text-white d-flex align-items-center justify-content-center" wire:ignore>
                        <h6 class="mb-0">Totais</h6>
                    </div>
                    <div class="card-body">
                        @livewire('incluir-totais', key(time()))
                    </div>
                </div>
                <div class="card" style="height: 200px;">
                    <div class="card-header text-center bg-primary text-white d-flex align-items-center justify-content-center" wire:ignore>
                        <h4 class="mb-0"><b>TOTAL</b></h4>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                          @livewire('total-sale', key(time()))
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="" style="padding: 10px">
               <span class="d-inline">
                    <button type="button" class="btn btn-success btn-sm" id="openModalBtnFecharVenda" {{ $totalItens > 0 ? '' : 'disabled' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                          <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
                        </svg>
                        Fechar Venda (F8)
                    </button>
                </span>
                <span class="d-inline">
                    <button type="button" class="btn btn-warning btn-sm" onclick="openModalPrintSale()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                          <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                          <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                        </svg>
                        Imprimir Venda (F7)
                    </button>
                </span>
                <span class="d-inline">
                    <button type="button" class="btn btn-danger btn-sm" onclick="cancelSale({{auth()->user()->id}})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        Cancelar Venda (F9)
                    </button>
                </span>
            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade right" id="slideInModal" tabindex="-1" aria-labelledby="slideInModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
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

    <!-- Modal -->
    <div class="modal fade top" id="slideInModalFecharVenda" tabindex="-1" aria-labelledby="slideInModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg p-0">
            <div class="modal-content p-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-monospace" id="slideInModalLabel">Fechamento de Venda - {{$codeSale}}</h5>
                    <button type="button" class="btn-close" id="closeModalBtn" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="card-container d-flex">
                        <div class="card d-flex flex-column">
                            <div class="card-body text-monospace">
                                @livewire('sale', key(time()))
                            </div>
                            <div class="card-footer text-right">
                                <div class="tooltip-wrapper" data-toggle="tooltip" title="{{$clienteId > 0 ? 'Gerar Link da Venda' : 'Associe um cliente à venda para criar o link'}} " >
                                    <button type="button" class="btn btn-success btn-sm text-monospace
                                        btn-finalizar-venda-link"  data-cliente_id="{{$clienteId}}" {{$clienteId > 0 ? '' : 'disabled' }}
                                    onclick="finalizeSale(`{{$codeSale}}`,`{{\App\Enums\StatusVenda::PENDENTE}}`)">Gerar Link</button>
                                </div>
                                <div class="tooltip-wrapper" data-toggle="tooltip" title="Finalizar Venda" id="btn-finalizar-venda">
                                    <button type="button" class="btn btn-primary btn-sm text-monospace btn-finalizar-venda"
                                            onClick="finalizeSale(`{{$codeSale}}`,`{{\App\Enums\StatusVenda::PAGO}}`)" disabled>
                                        Finalizar Venda
                                    </button>
                                </div>
                                <div class="tooltip-wrapper" data-toggle="tooltip" title="Fechar Tela">
                                    <button type="button" class="btn btn-outline-danger btn-sm text-monospace" id="closeModalFooterBtn"
                                            data-bs-dismiss="modal" title="Fechar Janela" data-toggle="tooltip">
                                        Fechar (ESC)
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Menu -->
    <div class="modal fade left" id="openMenuModal" tabindex="-1" aria-labelledby="slideInModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="slideInModalLabel">Menu</h5>
                    <button type="button" class="btn-close" id="closeMenuModal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-dark">
                    @livewire('menu')
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade right" id="openModalPrintSale" tabindex="-1" aria-labelledby="openModalPrintSaleLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="slideInModalLabel">Imprimir Venda</h5>
                    <button type="button" class="btn-close" id="closeModalPrintSale" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-container d-flex">
                        <div class="card search-card d-flex flex-column" style="flex: 3;">
                            <div class="card-body">
                                <div class="search-input">
                                    @livewire('search-sale')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@push("scripts")
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.7-beta.15/jquery.inputmask.min.js"></script>--}}
    <script src="{{URL::asset('js/custom.js')}}"></script>
@endpush
@push("styles")
    <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('css/modal.css') }}" />
@endpush
