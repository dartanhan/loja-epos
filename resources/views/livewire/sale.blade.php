<div>
    <style>
        /* Estilos customizados podem ser adicionados aqui */
        .item-list {
        max-height: 300px; /* Altura máxima para o div com scroll */
        overflow-y: auto; /* Adiciona scroll vertical se necessário */
        }
    </style>
    <div class="container">
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

        <!-- Forma de pagamento -->
        <div class="card">
            <div class="card-header">
            Forma de Pagamento
            </div>
            <div class="card-body">
            <select class="form-select mb-3">
                <option selected>Selecione a forma de pagamento...</option>
                <option value="1">Dinheiro</option>
                <option value="2">Cartão de Crédito</option>
                <option value="3">Cartão de Débito</option>
                <option value="4">PIX</option>
            </select>
            <button type="button" class="btn btn-primary">Finalizar Venda</button>
            </div>
        </div>

        <!-- Forma de entrega -->
        <div class="card">
            <div class="card-header">
            Forma de Entrega
            </div>
            <div class="card-body">
            <select class="form-select mb-3">
                <option selected>Selecione a forma de entrega...</option>
                <option value="moto-taxi">Moto Táxi</option>
                <option value="moto-uber">Moto Uber</option>
                <option value="entrega-rapida">Entrega Rápida</option>
                <option value="retirada-local">Retirada no Local</option>
            </select>
            <button type="button" class="btn btn-primary">Finalizar Venda</button>
            </div>
        </div>
    </div>
</div>
