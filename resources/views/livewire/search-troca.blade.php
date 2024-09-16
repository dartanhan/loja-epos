<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="card d-flex" wire:ignore>
        <div class="input-group">
            <input type="text" wire:model.defer="codeSale"  name="codeSale" id="codeSale" wire:ignore
                   placeholder="Digite o código da Venda KNXXXX" maxlength="11"
                   wire:keydown.enter="searchTroca"
                   class="form-control form-control-sm" style="padding: 0px 0px 0px 5px" autofocus>
            <div class="input-group-append">
                <button type="button" class="btn btn-primary " wire:click.defer="searchTroca" data-toggle="tooltip" title="Pesquisar Venda">
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

{{--    <div id="validation-errors" class="alert alert-danger mt-2" style="display: none;">--}}
{{--        <ul id="validation-errors-list"></ul>--}}
{{--    </div>--}}
    @if($cartItems)
        {{$cartItems}}
    @endif
</div>
