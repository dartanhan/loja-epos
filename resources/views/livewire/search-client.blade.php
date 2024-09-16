<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="card d-flex" wire:ignore>
                <div class="input-group">
{{--                    <div class="input-group-append">--}}
{{--                        <span class="input-group-text" >--}}
{{--                            <i class="fas fa-search"></i>--}}
{{--                        </span>--}}
{{--                    </div>--}}
                    <input type="text" wire:model.defer="cpfTelefone"  name="searchClient" id="searchClient"
                           placeholder="Digite o CPF ou Telefone" maxlength="11"
                           wire:keydown.enter="searchClient"
                           class="form-control form-control-sm" inputmode="numeric" pattern="[0-9]*"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');" style="padding: 0px 0px 0px 5px" autofocus>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary " wire:click.defer="searchClient" data-toggle="tooltip" title="Pesquisar Cliente">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
    </div>
    @if (session()->has('notfound'))
        <div class="alert alert-danger mt-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.5 3.5 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.5 4.5 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5"/>
            </svg>
            {{ session('notfound') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger mt-3 text-center">
        {{ session('error') }}
        </div>
    @endif

    @if (session()->has('message'))
        <div class="alert alert-success mt-3 text-center">{{ session('message') }}</div>
    @endif

    <div id="validation-errors" class="alert alert-danger mt-2" style="display: none;">
        <ul id="validation-errors-list"></ul>
    </div>

    @if ($client)
        <div class="card d-flex">
{{--            <div class="card-header d-flex align-items-center">--}}
{{--                <label for="cliente" class="form-label mb-0 mr-2">--}}
{{--                    <h5>--}}
{{--                        Cliente: <span class="text-black">{{ $client->nome }}</span>--}}
{{--                    </h5>--}}
{{--                </label>--}}
{{--            </div>--}}
            <div class="card-body">

                    <input type="hidden"  wire:model="clienteId" name="clienteId" id="clienteId">
                    <div class="row">
                        <div class="form-group mb-3">
                            <div class="form-floating">
                                <input type="text" id="nome" wire:model.defer="nome" name="nome"
                                       class="form-control">
                                <label for="nome" class="form-label mb-0 text-monospace">Nome</label>
                                @error('nome') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" id="cpf" wire:model.defer="cpf" name="cpf"
                                   class="form-control form-control-sm" maxlength="11">
                                    <label for="cpf" class="form-label mb-0 text-monospace">CPF</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" id="telefone" wire:model.defer="telefone" name="telefone"
                                   class="form-control form-control-sm" maxlength="11">
                                <label for="telefone" class="form-label mb-0 text-monospace">Telefone</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group mb-3">
                            <div class="form-floating">
                                <input type="email" id="email" wire:model.defer="email" name="email"
                                   class="form-control form-control-sm">
                                    <label for="email" class="form-label mb-0 text-monospace">Email</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input type="text" wire:model.defer="cep" name="cep" placeholder="Digite o CPF" maxlength="8"
                                       class="form-control form-control-sm" inputmode="numeric" pattern="[0-9]*"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                        <label for="cep" class="form-label mb-0 text-monospace">Cep</label>
                                </div>
                                <div class="input-group-append mt-1 p-1">
                                    <button type="button" class="btn btn-primary btn-lg" wire:click.defer="searchCep" data-toggle="tooltip" title="Pesquisar CEP">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="form-floating">
                                <input type="text" id="numero" wire:model.defer="numero" name="numero" maxlength="5"
                                       class="form-control form-control-sm">
                                    <label for="numero" class="form-label mb-0 text-monospace">Número</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group mb-3">
                            <div class="form-floating">
                                <textarea type="text" id="logradouro" wire:model.defer="logradouro" name="logradouro"
                                      class="form-control form-control-sm"></textarea>
                                 <label for="logradouro" class="form-label mb-0 text-monospace">Endereço</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" id="complemento" wire:model.defer="complemento" name="complemento"
                                       class="form-control form-control-sm">
                                <label for="complemento" class="form-label mb-0 text-monospace">Complemento</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" id="bairro" wire:model.defer="bairro" name="bairro"
                                   class="form-control form-control-sm">
                                <label for="bairro" class="form-label mb-0 text-monospace">Bairro</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="form-group col-md-6">
                            <div class="form-floating">
                                <input type="text" id="localidade" wire:model.defer="localidade" name="localidade"
                                   class="form-control form-control-sm">
                                <label for="bairro" class="form-label mb-0 text-monospace">Estado</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="form-floating">
                                <input type="text" id="uf" wire:model.defer="uf" name="uf"
                                   class="form-control form-control-sm" maxlength="2">
                                    <label for="bairro" class="form-label mb-0 text-monospace">UF</label>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="form-floating">
                                <input type="text" id="taxa" wire:model.defer="taxa" name="taxa"
                                    class="form-control form-control-sm">
                                    <label for="taxa" class="form-label mb-0 text-monospace ">Taxa</label>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm text-monospace "
                            wire:click.defer="saveClient" title="Salvar/Atualizar dados do Cliente" data-toggle="tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                            </svg> Salvar
                    </button>
                    <button type="button" class="btn btn-success btn-sm text-monospace" data-bs-dismiss="modal"
                            wire:click.defer="associarCliente" title="Incluir Cliente na Venda" data-toggle="tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg> Incluir Cliente
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm text-monospace" id="closeModalFooterBtn"
                            data-bs-dismiss="modal" title="Fechar Janela" data-toggle="tooltip">
                        Fechar (ESC)
                    </button>
            </div>

            <script>
                $(document).ready(function() {
                    //$('[data-toggle="tooltip"]').tooltip();
                    $('#taxa').on('input', function(event) {
                        // Pega o valor atual do input
                        let valor = $(this).val();

                        // Remove tudo que não for número ou ponto
                        valor = valor.replace(/\D/g, '');

                        // Formata o valor para moeda
                        valor = (parseInt(valor) / 100).toFixed(2).toString().replace('.', ',');

                        // Adiciona R$ na frente do valor
                        valor = 'R$ ' + valor;

                        // Atualiza o valor do input
                        $(this).val(valor);
                    });
                });
            </script>
        </div>
    @endif
</div>
