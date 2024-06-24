<script src="{{URL::asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
<script src="{{URL::asset('bootstrap/js/popper.min.js') }}"></script>
<script src="{{URL::asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{URL::asset('assets/js/loader.js') }}"></script>
<script src="{{URL::asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
<script src="{{URL::asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{URL::asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
<script src="{{URL::asset('assets/fontawesome/js/all.min.js')}}"></script>
<script src="{{URL::asset('plugins/input-mask/jquery.maskMoney.min.js')}}"></script>

{{--<script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>--}}
{{--<script src="{{ asset('assets/js/app.js') }}"></script>--}}
{{--<script src="{{ asset('js/onscan.js') }}"></script>--}}
{{--<script src="{{ asset('plugins/input-mask/jquery.inputmask.bundle.min.js') }}"></script>--}}


{{--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />--}}
{{--<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>--}}
{{--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">--}}

{{--<script src="{{ asset('plugins/nicescroll/nicescroll.js')}}"></script>--}}
{{--<script src="{{ asset('plugins/currency/currency.js')}}"></script>--}}
{{--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>--}}
{{--<script src="{{ asset('/js/jquery.countdown.js') }}"></script>--}}
{{--<script src="{{ asset('js/apexcharts.js') }}"></script>--}}
<script>
    $(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();


    /***
     * PESQUISA DE PRODUTOS
     * */

        $("#searchProduct").autocomplete({
            minLength: 2,
            source: function(request, response) {
                if(request.term.trim()){
                    $.get('http://127.0.0.1/loja-epos/search', { term: request.term }, function(data) {
                       // console.log(data);
                        // Mapeie os dados para o formato que o autocomplete espera
                        const formattedData = data.map(elemento => ({
                            label: elemento.subcodigo +" - "+ elemento.produto_descricao + " - " + elemento.variacao,
                            value: elemento.subcodigo +" - "+ elemento.produto_descricao + " - " + elemento.variacao, // Valor a ser inserido no input quando um item é selecionado
                            subcodigo: elemento.subcodigo,
                            variacaoId: elemento.id,
                            //descricao: elemento.produto_descricao + " - " + elemento.variacao,
                            // quantidade: elemento.quantidade,
                        }));

                        // Verifique se há dados para exibir
                        if (formattedData.length === 0) {
                            formattedData.push({
                                label: 'Nenhum produto encontrado',
                                value: '', // Pode definir como vazio ou outro valor padrão
                            });
                        }
                        // Chame a função response com os dados formatados
                        response(formattedData);
                    });
                }
            },
            select: function(event, ui) {
                Livewire.emit('addToCart', ui.item.subcodigo,ui.item.variacaoId );
                document.getElementById('searchProduct').focus();
               // $('#openModalBtn').prop('disabled', false);
                // Limpe o campo de pesquisa
                // setTimeout(() => {
                //     $("#searchProduct").val('');
                //     //Adicona ao focus ao input, de pesqusia de produtos
                //
                // }, 500);
           }

        });
    /****************
     * **************
     * ****************/



    /**********************
     * *********************
     * *********************/

        // $( '#searchProduct' ).select2( {
        //     theme: 'bootstrap4'
        // } );
    });


    /**
     * FORMATAÇÂO DE MOEDA
     *
     * */
    /***
     * FORMATA CAMPO COM MOEDA
     *
     * OnkeyPress
     * */
    function formatMoneyPress(parm) {
        let valor = parm.value;

        valor = valor + '';
        valor = parseInt(valor.replace(/[\D]+/g, ''));
        valor = valor + '';
        valor = valor.replace(/([0-9]{2})$/g, ",$1");

        if (valor.length > 6) {
            valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
        }

        parm.value = valor;
        if(valor === 'NaN') parm.value = '';
    }
    /**********************
     * *********************
     * *********************/

{{--    const formatoMoeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });--}}
{{--    let valorFormatado = "", valorTotal = 0;--}}
{{--    let options = [];--}}
{{--    let option = "";--}}
{{--    let formaPgto = "";--}}
{{--    let tipoVenda = "";--}}
    const colorRed = "#e7515a";
    const colorGreen = "#02b84a";
    const colorBlack = "#484948";
    const iconCartAdd = "<i class=\"fa-solid fa-cart-plus fa-bounce\"></i>&nbsp;";
    const iconCartRemove = "<i class=\"fa-solid fa-cart-arrow-down fa-fade\"></i>&nbsp;";
    const iconCartNotStock = "<i class=\"fa-solid fa-truck fa-fade\"></i>&nbsp;";
    const iconCartNotFound = "<i class=\"fa-solid fa-triangle-exclamation fa-bounce\"></i>&nbsp;";
    const iconCartEmpty = "<i class=\"fa-solid fa-recycle fa-spin\"></i>";

{{--    /**--}}
{{--     * Exibe as notificações na aplicação--}}
{{--     * */--}}
    let noty = function(msg, color, icon)
    {
        Snackbar.show({
            text:  icon + msg.toUpperCase(),
            actionText: '',
            actionTextColor: '#fff',
            backgroundColor: color,
            pos: 'top-right'
        });
    }

{{--    /**--}}
{{--     * Notificações disparadas pelo liveware--}}
{{--     * */--}}
    document.addEventListener('DOMContentLoaded', function() {

        window.livewire.on('global-error', msg => {
            Swal.fire({
                icon: "danger",
                html: msg,
                showConfirmButton: false,
                timer: 2500
            });
        });

        window.livewire.on('global-msg', msg => {
            noty(msg,colorBlack,iconCartEmpty);
        });

        window.livewire.on('scan-ok', msg => {
            noty(msg,colorGreen,iconCartAdd);
        });

        window.livewire.on('scan-notfound', msg => {
            noty(msg,colorRed,iconCartNotFound);
        });

        window.livewire.on('no-stock', msg => {
            noty(msg,colorRed,iconCartNotStock);
        });

        window.livewire.on('scan-remove', msg => {
            noty(msg,colorRed,iconCartRemove);
        });

        window.livewire.on('focus-input-search', msg => {
            document.getElementById('searchProduct').focus();
        });

         window.livewire.on('refresh', msg => {
             if(msg)
                 window.location.reload(msg);
         });

        // $('#taxa').on('change', function() {
        //     console.log($(this).val());
        //     window.livewire.emit('atualizarTaxa', $(this).val());
        // });
});

    /**
     * CAREEGa o tooltip após o livewire atualizar
     * */
    function activateTooltipsAndFormatting() {
        $('[data-toggle="tooltip"]').tooltip();
    }


    document.addEventListener('livewire:load', function() {
        //Adicona ao focus ao input, de pesqusia de produtos
        //document.getElementById('searchProduct').focus();

        /**
         * CAREEGa o tooltip após o livewire atualizar
         * */
        activateTooltipsAndFormatting();
        window.livewire.hook('message.processed', (message, component) => {
            activateTooltipsAndFormatting();

        });

        /**
         * Exibe as mensagens de erro de validação da clientes
         * */
        Livewire.on('validationError', errors => {
            let errorsList = document.getElementById('validation-errors-list');
            errorsList.innerHTML = '';

            errors.forEach(error => {
                let li = document.createElement('li');
                li.textContent = error;
                errorsList.appendChild(li);
            });

            document.getElementById('validation-errors').style.display = 'block';
        });

        /***
         * MODAL CORTINA
         * **/
        $('#openModalBtn').on('click', function () {

            $('#slideInModal').modal({
                // backdrop: 'static',  // Disables closing the modal by clicking outside of it
                keyboard: false      // Disables closing the modal with the ESC key
            }).modal('show');

            //Adicona ao focus ao input, após abrir a modal
            const searchClient = document.getElementById('searchClient');
            setTimeout(() => {
                searchClient.focus();
            }, 500);
        });

        // Close the modal when the ESC key is pressed
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                $('#slideInModal').modal('hide');
                $('#slideInModalFecharVenda').modal('hide');
                focusInputSearch();
            }
        });

        // Prevent the modal from closing when the close button is clicked
        $('#closeModalBtn, #closeModalFooterBtn').on('click', function (e) {
            e.preventDefault();
            focusInputSearch();
        });

        /**
         * Ao fechar o modal reseta as informações
         * */
        $('#slideInModal').on('hidden.bs.modal', function () {
           Livewire.emit('resetInputFields');
        });

        $('#openModalBtnFecharVenda').on('click', function () {
            $('#slideInModalFecharVenda').modal({
                backdrop: 'static',  // Disables closing the modal by clicking outside of it
                keyboard: false      // Disables closing the modal with the ESC key
            }).modal('show');

        });
    });

    function focusInputSearch() {
        console.log("foi");
        //Adicona ao focus ao input, após abrir a modal
        const searchProduct = document.getElementById('searchProduct');
        setTimeout(() => {
            searchProduct.focus();
        }, 500);
    }
function Confirma(id, eventName, text) {
    Swal({
        title: 'CONFIRMAR',
        text: text,
        type: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Fechar',
        cancelButtonColor: '#fff',
        confirmButtonColor: '#bb0a30',
        confirmButtonText: 'Remover'
    }).then(function(result) {
        if (result.value) {
           // console.log("eventName >> " + eventName,id);
            window.livewire.emit(eventName, id);
            //window.livewire.emit("clienteAtualizado");
            Swal.close();
        }

    })
}

{{--        //coloca o código da venda no hidden--}}
{{--        window.livewire.on('codigo-venda', codigo => {--}}
{{--          //  console.log(codigo);--}}
{{--           $("#codigoVenda").val(codigo);--}}
{{--           //se codigo null, carrinho vazio, fecha modal--}}
{{--           if(codigo === null){--}}
{{--               $('#meuModal').modal('hide');--}}
{{--               $('#modal2').modal('hide');--}}
{{--           }--}}
{{--        });--}}

{{--        /**--}}
{{--         * Regra--}}
{{--         * faz um loop nam tabela buscando o input da quantidade e--}}
{{--         * pinta com cor red no caso da rera de 5 iguais ou 12 diferenetes--}}
{{--         */--}}
{{--        window.livewire.on('pinta-linha-atacado', data  => {--}}
{{--          //  console.log('pinta-linha-atacado');--}}
{{--            pintaLinha();--}}
{{--        });--}}

{{--        //onload da tabela--}}
{{--        pintaLinha();--}}
{{--   });--}}
{{--    /**--}}
{{--     * Altera a abela do Cart para exibir uma cor dioferente caso satifaça a regra--}}
{{--     * de atacado 5 iguais ou 12 diferentes--}}
{{--     * */--}}
{{--    function pintaLinha() {--}}
{{--        let tabela = document.getElementById("tableCart");--}}

{{--        if(tabela !== null){--}}

{{--            let linha = 0;--}}

{{--            // Obtém todas as linhas da tabela, exceto a primeira no tbody (índice 0)--}}
{{--            let linhas = tabela.querySelectorAll("tbody tr");--}}

{{--            /**--}}
{{--             * Pinta todas as linhas de vermelho acima de 10, regra atacado--}}
{{--             * */--}}
{{--            if( linhas.length >= 10 ){--}}
{{--                for (let i = 0; i < linhas.length; i++) {--}}
{{--                    // Adiciona a classe--}}
{{--                    linhas[i].classList.add('linha-vermelha');--}}
{{--                }--}}
{{--            }--}}

{{--            if( linhas.length < 10 ){--}}
{{--            // Itera sobre as linhas--}}
{{--                for (let i = 0; i < linhas.length; i++) {--}}
{{--                    linha = linhas[i];--}}

{{--                    // Obtém a célula (td) que contém o input--}}
{{--                    let celulaQuantidade = linha.getElementsByTagName("td")[2];--}}

{{--                    // Verifica se a célula existe e se contém um input--}}
{{--                    if (celulaQuantidade && celulaQuantidade.querySelector("input")) {--}}
{{--                        // Obtém o valor do input dentro da célula--}}
{{--                        let valorQuantidade = celulaQuantidade.querySelector("input").value;--}}

{{--                        // Converte o valor para número--}}
{{--                        let valorNumerico = parseInt(valorQuantidade);--}}
{{--                        console.log(valorNumerico);--}}
{{--                        // Remove a classe existente--}}
{{--                        linha.classList.remove("linha-vermelha");--}}

{{--                        /**--}}
{{--                         * Pinta vermelho acima de 5 o produto, regra atacado--}}
{{--                         * */--}}
{{--                        if (valorNumerico >= 5) {--}}
{{--                            linha.classList.add('linha-vermelha');--}}
{{--                        }--}}
{{--                    }--}}
{{--                }--}}

{{--            }--}}
{{--        }--}}
{{--    }--}}

{{--    /**--}}
{{--     *--}}
{{--     * */--}}
{{--    function converterFormatoMoeda(valor) {--}}
{{--        // Remove o "R$ " e substitui a vírgula por ponto--}}
{{--        return valor !== "" ? parseFloat(valor.replace("R$ ", "").replace(",", ".")) : 0;--}}
{{--    }--}}

{{--    function isModalAberto() {--}}
{{--        return $('#meuModal').hasClass('show');--}}
{{--    }--}}


{{--    /**--}}
{{--     * Captura o evento do teclado e realiza as ações--}}
{{--     * **/--}}

{{--    document.addEventListener('keydown', function(e) {--}}

{{--        let valorRecebido = 0;--}}
{{--        let valorTroco = 0;--}}
{{--        let abreModal = false;--}}

{{--        //Fechamento da venda F4--}}
{{--        if (e.key === 'F4') {  // 115 é o código da tecla F4--}}
{{--            e.preventDefault();--}}
{{--            valorRecebido = converterFormatoMoeda($("#valorPagar").val());--}}
{{--            console.log(valorRecebido);--}}

{{--            // Verifica se tem modal aberto do fechamento do pagamento--}}
{{--            if (!isModalAberto()) {--}}
{{--                defaultMessageDialog("Selecione a forma de Pagamento!", "question");--}}
{{--                abreModal = false;--}}
{{--                return false;--}}
{{--            }--}}

{{--            //caso tipo seja dinheiro F1--}}
{{--            if ( $('#tipoVenda').val() === "F1" && valorRecebido > 0) {--}}
{{--                valorTroco = valorRecebido - $("#hiddenTotal").val();--}}
{{--                //console.log(valorTroco);--}}
{{--                if (valorTroco < 0) {--}}
{{--                    defaultMessageDialog("Valor recebido inferior ao total da venda!");--}}
{{--                    return;--}}
{{--                }--}}

{{--                // Abre a segunda modal(SALVAR VENDA) ou executa outra ação aqui--}}
{{--                //$('#modal2').modal('show');--}}
{{--                //Livewire.emitTo('cart-component', 'saveSale', $("#codigoVenda").val(), $('#hiddenTotal').val(), $('#tipoVenda').val());--}}

{{--                abreModal = true;--}}

{{--            } else if($('#tipoVenda').val() === "F1" && valorRecebido <= 0) {--}}
{{--                    defaultMessageDialog("Informe o valor recebido da venda?");--}}
{{--                    abreModal = false;--}}
{{--                    return false;--}}
{{--            }--}}
{{--            //caso tipo seja cartão / pix e etc F2,F3--}}
{{--            if ( $('#tipoVenda').val() === "F2" || $('#tipoVenda').val() === "F3") {--}}

{{--                abreModal = true;--}}
{{--            }--}}

{{--            // Abre a segunda modal(SALVAR VENDA) ou executa outra ação aqui--}}
{{--            if(abreModal){--}}
{{--                $('#modal2').modal('show');--}}
{{--                Livewire.emitTo('cart-component', 'saveSale', $("#codigoVenda").val(), $('#hiddenTotal').val(), $('#tipoVenda').val());--}}

{{--                //reseta as informações do produto na tela--}}
{{--                document.getElementById('quantidade').innerText = '';--}}
{{--                document.getElementById('valor_cartao_pix').innerText = '';--}}
{{--                document.getElementById('valor_atacado').innerText = '';--}}
{{--                document.getElementById('valor_parcelado').innerText = '';--}}
{{--                document.getElementById('valor_lista').innerText = '';--}}
{{--                document.getElementById('imagem').innerText = '';--}}
{{--            }--}}

{{--        }--}}

{{--       // Teclas F..--}}
{{--        if (e.key  === 'F1' || e.key  === 'F2' || e.key  === 'F3' ) {--}}
{{--            e.preventDefault();--}}
{{--            console.log(e.key);--}}

{{--            /**--}}
{{--             * Emite para o livewire a açõa de criar a venda do CartComponent--}}
{{--             * CartComponent.php--}}
{{--             * */--}}
{{--            Livewire.emit('createSale');--}}

{{--            /**--}}
{{--             * Pega as informações da Cart--}}
{{--             * */--}}
{{--            Livewire.on('informacoesAtualizadas', (data) => {--}}
{{--                // Manipular os dados recebidos--}}
{{--                console.log('Dados recebidos:', data);--}}

{{--                const isEmpty = data.length === 0 ? true : false;--}}
{{--                if(isEmpty){--}}
{{--                    defaultMessageDialog('Sem vendas a serem finalizadas!');--}}
{{--                }else {--}}
{{--                    <!-- cart.blade.php -->--}}
{{--                    // Faça o que for necessário com os dados, por exemplo, atualizar a interface do usuário--}}
{{--                    $('#botaoAbrirModal').trigger('click');--}}

{{--                    switch (e.key) {--}}
{{--                        case 'F1':--}}
{{--                            options = [{value: '1', text: 'DINHEIRO'}];--}}
{{--                            formaPgto = "DINHEIRO";--}}
{{--                            valorFormatado = formatoMoeda.format(data.totalGeralDinner);--}}
{{--                            valorTotal = data.totalGeralDinner;--}}
{{--                            break;--}}
{{--                        case 'F2':--}}
{{--                            options = [--}}
{{--                                {value: '10', text: 'PIX'}, //pix--}}
{{--                                {value: '9', text: 'DÉBITO'}, //Débito elo--}}
{{--                                {value: '2', text: 'CRÉDITO (1X)'}, //Credito master e visa--}}
{{--                                {value: '4', text: 'CRÉDITO (2X)'} //Parcelado 2x Visa/Master--}}
{{--                            ];--}}
{{--                            formaPgto = "PIX, DÉBITO E CRÉDITO(ATÉ 2X)";--}}
{{--                            valorFormatado = formatoMoeda.format(data.totalGeralPDC);--}}
{{--                            valorTotal = data.totalGeralPDC;--}}
{{--                            break;--}}
{{--                        case 'F3':--}}
{{--                            options = [--}}
{{--                                {value: '5', text: 'CRÉDITO (3X) '}, //Parcelado 3x Visa/Master--}}
{{--                                {value: '6', text: 'CRÉDITO (4X) '}, //Parcelado 4x Visa/Master--}}
{{--                                {value: '7', text: 'CRÉDITO (5X) '}, //Parcelado 5x Visa/Master--}}
{{--                                {value: '8', text: 'CRÉDITO (6X) '} //Parcelado 6x Visa/Master--}}
{{--                            ];--}}
{{--                            formaPgto = "CRÉDITO (3X À 6X)";--}}
{{--                            valorFormatado = formatoMoeda.format(data.totalGeralCredito);--}}
{{--                            valorTotal = data.totalGeralCredito;--}}
{{--                            break;--}}
{{--                    }--}}
{{--                    //Dinheiro--}}
{{--                    if(options.length === 1){--}}
{{--                        $('#divCartao').css('display',  "none");//esconde div froma de pagmamento--}}
{{--                        //$("#divBandeiraCartao").css("display", "none"); //esconde div bandeira--}}
{{--                        $("#divDinner").css("display", "block");//exibe div dinheiro--}}
{{--                        $('#meuModal').on('shown.bs.modal', function () {--}}
{{--                            $('#valorPagar').focus();--}}
{{--                        });--}}

{{--                    }else {--}}
{{--                        // Selecione o elemento select--}}
{{--                        const select = document.getElementById('formaPgto');--}}
{{--                        // Limpe as opções existentes no select--}}
{{--                        select.innerHTML = "<option>Forma de Pagamento</option>";--}}

{{--                        $('#divCartao').css('display',  "block"); //exibe div froma de pagmamento--}}
{{--                        //$("#divBandeiraCartao").css("display", "block"); //exibe div bandeira--}}
{{--                        $("#divDinner").css("display", "none"); //esconde div dinheiro--}}

{{--                        // Adicione as opções ao select--}}
{{--                        options.forEach(opcao => {--}}
{{--                            option = document.createElement('option');--}}
{{--                            option.value = opcao.value;--}}
{{--                            option.text = opcao.text;--}}
{{--                            select.appendChild(option);--}}
{{--                        });--}}
{{--                    }--}}

{{--                    $("#header").html("FORMA DE PAGAMENTO ESCOLHIDA: <p><strong><h2>" + formaPgto + "</h2></strong></p>");--}}

{{--                    /**--}}
{{--                     * Dados do carrinho retornardo pelo Livewire--}}
{{--                     * */--}}
{{--                    // $.each(data, function (index, value) {--}}
{{--                    //     console.log(index + ": " + value.variacao_id);--}}
{{--                    //--}}
{{--                    // });--}}

{{--                    $("#valorTotalVenda").html("<h2>"+valorFormatado+"</h2>");--}}

{{--                    // Verificar se o input já existe--}}
{{--                    const hiddenTotalExistente = document.getElementById('hiddenTotal');--}}
{{--                    const tipoVenda = document.getElementById('tipoVenda');--}}

{{--                    if (hiddenTotalExistente) {--}}
{{--                        // Se existir, apenas atualize o valor--}}
{{--                        hiddenTotalExistente.value = valorTotal;--}}
{{--                        tipoVenda.value = e.key;--}}
{{--                    } else {--}}
{{--                        // Se não existir, crie um novo--}}
{{--                        const hiddenTotal = document.createElement('input');--}}
{{--                        hiddenTotal.value = valorTotal;--}}
{{--                        hiddenTotal.type = "hidden";--}}
{{--                        hiddenTotal.id = "hiddenTotal";--}}
{{--                        $("#modalFecharVenda").append(hiddenTotal);--}}

{{--                        const tipoVenda = document.createElement('input');--}}
{{--                        tipoVenda.value = e.key;--}}
{{--                        tipoVenda.type = "hidden";--}}
{{--                        tipoVenda.id = "tipoVenda";--}}
{{--                        $("#modalFecharVenda").append(tipoVenda);--}}
{{--                    }--}}
{{--                }--}}
{{--            });--}}

{{--            /***--}}
{{--             * Ao digitar o valor no campo valorPagar da modal , calcula o troco--}}
{{--             * */--}}
{{--            // Máscara de moeda para o campo #valorPagar--}}
{{--            $('#valorPagar').inputmask("currency", {--}}
{{--                radixPoint: ",",--}}
{{--                groupSeparator: ".",--}}
{{--                autoGroup: true,--}}
{{--                prefix: 'R$ ',--}}
{{--                rightAlign: false,--}}
{{--                removeMaskOnSubmit: true,--}}
{{--                numericInput: true,--}}
{{--                placeholder: "0"--}}
{{--            });--}}

{{--            /**--}}
{{--             * Ao digitar calcula o troco--}}
{{--             * */--}}
{{--            $("#valorPagar").on('keyup',function() {--}}

{{--                // Obter o valor do campo valorPagar--}}
{{--                const valorPagar = parseFloat($(this).val().replace(/[^\d.,-]/g, '').replace(',', '.')) || 0;--}}

{{--                // Obter o valor total do hiddenTotal--}}
{{--                const valorTotal = parseFloat($("#hiddenTotal").val()) || 0;--}}

{{--                // Calcular o troco--}}
{{--                const troco = valorPagar - valorTotal;--}}

{{--                // Formatando o troco como moeda--}}
{{--                const trocoFormatado = formatoMoeda.format(troco);--}}

{{--                if(troco < 0){--}}
{{--                    $("#troco").css('color', 'red'); // Exemplo: altera a cor do texto para vermelho--}}
{{--                }else{--}}
{{--                    $("#troco").css('color', 'black');--}}
{{--                }--}}
{{--                // Exibir o troco no campo #troco--}}
{{--                $("#troco").val(trocoFormatado);--}}
{{--            });--}}

{{--            // window.livewire.emitTo('cart-component','fecharVenda', (dados) => {--}}
{{--            //     console.log('Dados recebidos:', dados);--}}
{{--            // });--}}

{{--            /*var el1 = document.getElementById('lang')--}}
{{--            var el2 = document.getElementById('body')--}}
{{--            var el3 = document.getElementById('container')--}}


{{--            if(el1.classList.contains('sidebar-noneoverflow')) {--}}

{{--                el1.classList.remove("sidebar-noneoverflow")--}}
{{--                el2.classList.remove("sidebar-noneoverflow")--}}
{{--                el3.classList.remove("sidebar-closed","sbar-open")--}}

{{--            } else {--}}

{{--                el1.classList.add("sidebar-noneoverflow")--}}
{{--                el2.classList.add("sidebar-noneoverflow")--}}
{{--                el3.classList.add("sidebar-closed","sbar-open")--}}
{{--            }*/--}}
{{--        }--}}

{{--    });--}}
{{--    /**--}}
{{--     * Confirmação de exclusão--}}
{{--     * */--}}

{{--    function Confirm(id, eventName, text) {--}}
{{--        Swal.fire({--}}
{{--            title: 'CONFIRMAR',--}}
{{--            text: text,--}}
{{--            icon: 'question',--}}
{{--            showCancelButton: true,--}}
{{--            cancelButtonText: 'Não',--}}
{{--            cancelButtonColor: '#d33',--}}
{{--            confirmButtonColor: '#3085d6',--}}
{{--            confirmButtonText: 'Sim'--}}
{{--        }).then(function(result) {--}}
{{--           // console.log(eventName, id);--}}
{{--            if (result.value) {--}}
{{--                window.livewire.emit(eventName, id)--}}
{{--               // window.livewire.emitTo('cart-component','eventoDeA', id)--}}
{{--                swal.close()--}}
{{--            }--}}

{{--        });--}}
{{--    }--}}

{{--    /**--}}
{{--     *--}}
{{--     * */--}}
{{--    let defaultMessageDialog  = function(html = 'titulo', icon = 'warning', position = "center"){--}}
{{--        Swal.fire({--}}
{{--            position: position,--}}
{{--            icon: icon,--}}
{{--            html: "<h4>" + html + "</h4>",--}}
{{--            showConfirmButton: false,--}}
{{--            timer: 2500--}}
{{--        });--}}
{{--    }--}}


{{--    // Livewire.on('scan-code-byid', postId => {--}}
{{--    //     Snackbar.show({--}}
{{--    //         text: "OK",--}}
{{--    //         actionText: 'FECHAR',--}}
{{--    //         actionTextColor: '#fff',--}}
{{--    //         backgroundColor: postId == 1 ? '#3b3f5c' : '#e7515a',--}}
{{--    //         pos: 'top-right'--}}
{{--    //     });--}}
{{--    // })--}}
</script>
{{--<script src="{{URL::asset('assets/js/custom.js') }}"></script>--}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


{{--<script src="{{ asset('plugins/flatpickr/flatpickr.js')}}"></script>--}}
{{--<script src="{{ asset('assets/js/custom.js') }}"></script>--}}
