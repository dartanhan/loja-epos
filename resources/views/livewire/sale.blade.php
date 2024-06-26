<div>
    <style>
        /* Estilos customizados podem ser adicionados aqui */
        .item-list {
        max-height: 300px; /* Altura máxima para o div com scroll */
        overflow-y: auto; /* Adiciona scroll vertical se necessário */
        }
    </style>

    <div class="container">
        <div class="row">
            <!--Tipo de Venda -->
            <div class="col-md-4">
                <div class="card mb-2">
                    @livewire('tipo-venda')
                </div>
            </div>

            <!-- Forma de pagamento -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-header text-monospace text-center bg-primary text-white p-2">
                        Forma de Pagamento
                    </div>
                    <div class="card-body text-monospace">
                        <select class="form-select mb-3">
                            <option selected>Selecione uma opção ?</option>
                            <option value="1">Dinheiro</option>
                            <option value="2">Cartão de Crédito</option>
                            <option value="3">Cartão de Débito</option>
                            <option value="4">PIX</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Forma de entrega -->
            <div class="col-md-4">
                   @livewire('forma-entrega')
            </div>
        </div>
        <!-- Lista de produtos com imagens -->
        <div class="card mb-3">
            <div class="card-header">
            Itens do Pedido
            </div>
            <div class="card-body item-list">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="https://via.placeholder.com/50" alt="Produto 1" class="me-3 rounded" style="max-width: 50px;">
                    Produto 1
                </div>
                <span class="badge bg-primary rounded-pill">R$ 50,00</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="https://via.placeholder.com/50" alt="Produto 2" class="me-3 rounded" style="max-width: 50px;">
                    Produto 2
                </div>
                <span class="badge bg-primary rounded-pill">R$ 30,00</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="https://via.placeholder.com/50" alt="Produto 3" class="me-3 rounded" style="max-width: 50px;">
                    Produto 3
                </div>
                <span class="badge bg-primary rounded-pill">R$ 20,00</span>
                </li>
                <!-- Adicione mais itens conforme necessário -->
            </ul>
            </div>
        </div>

        <!-- Total da venda -->
        <div class="card mb-3">
            <div class="card-header">
            Total da Venda
            </div>
            <div class="card-body">
            <h5 class="card-title">R$ 100,00</h5>
            </div>
        </div>

    </div>
</div>
