<div xmlns:wire="http://www.w3.org/1999/xhtml">

    @if($cartItems->isNotEmpty() && optional($cartItems->first()->clientes)->isNotEmpty())
        <span class="ml-3 text-danger remover-cliente-associado" title="Clique para remover o Cliente" data-toggle="tooltip"
              data-cliente-id="{{$cartItems[0]->clientes[0]->id}}" data-user-id="{{$userId}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
            </svg>
        </span>
        <span class="d-inline cliente-associado ml-2" title="Clique para Alterar o Cliente" data-toggle="tooltip">
            <h5 class="d-inline mb-0" id="openModalBtn">
                {{$cartItems[0]->clientes[0]->nome}}
            </h5>
        </span>

    @else
        <span class="d-inline" title="Associar Cliente à Venda" data-toggle="tooltip">
            <button type="button" class="btn btn-primary btn-sm" id="openModalBtn" >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-heart" viewBox="0 0 16 16">
                  <path d="M9 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h10s1 0 1-1-1-4-6-4-6 3-6 4m13.5-8.09c1.387-1.425 4.855 1.07 0 4.277-4.854-3.207-1.387-5.702 0-4.276Z"/>
                </svg> Incluir Cliente
            </button>
        </span>
    @endif
</div>
