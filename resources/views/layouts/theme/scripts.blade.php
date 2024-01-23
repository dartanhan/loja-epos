<script src="{{ asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('js/onscan.js') }}"></script>
<script src="{{ asset('plugins/input-mask/jquery.inputmask.bundle.min.js') }}"></script>



<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<script>
    $(document).ready(function() {
   //     App.init();
        $( '#formaPgto,#bandeiraCartao' ).select2( {
            theme: 'bootstrap4'
        } );
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

<script src="{{ asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
<script src="{{ asset('plugins/nicescroll/nicescroll.js')}}"></script>
<script src="{{ asset('plugins/currency/currency.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('/js/jquery.countdown.js') }}"></script>


<script>
    const formatoMoeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    let valorFormatado = "", valorTotal = 0;
    let options = [];
    let option = "";
    let formaPgto = "";
    let tipoVenda = "";
    const colorRed = "#e7515a";
    const colorGreen = "#02b84a";
    const colorBlack = "#484948";
    const iconCartAdd = "<i class=\"fa-solid fa-cart-plus fa-bounce\"></i>&nbsp;";
    const iconCartRemove = "<i class=\"fa-solid fa-cart-arrow-down fa-fade\"></i>&nbsp;";
    const iconCartNotStock = "<i class=\"fa-solid fa-truck fa-fade\"></i>&nbsp;";
    const iconCartNotFound = "<i class=\"fa-solid fa-triangle-exclamation fa-bounce\"></i>&nbsp;";
    const iconCartEmpty = "<i class=\"fa-solid fa-recycle fa-spin\"></i>";

    /**
     * Exibe as notificações na aplicação
     * */
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

    /**
     * Notificações disparadas pelo liveware
     * */
    document.addEventListener('DOMContentLoaded', function() {
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

        //coloca o código da venda no hidden
        window.livewire.on('codigo-venda', codigo => {
          //  console.log(codigo);
           $("#codigoVenda").val(codigo);
           //se codigo null, carrinho vazio, fecha modal
           if(codigo === null){
               $('#meuModal').modal('hide');
               $('#modal2').modal('hide');
           }
        });

        /**
         * Regra
         * faz um loop nam tabela buscando o input da quantidade e
         * pinta com cor red no caso da rera de 5 iguais ou 12 diferenetes
         */
        window.livewire.on('pinta-linha-atacado', data  => {
          //  console.log('pinta-linha-atacado');
            pintaLinha();
        });

        //onload da tabela
        pintaLinha();
   });
    /**
     * Altera a abela do Cart para exibir uma cor dioferente caso satifaça a regra
     * de atacado 5 iguais ou 12 diferentes
     * */
    function pintaLinha() {
        let tabela = document.getElementById("tableCart");

        if(tabela !== null){

            let linha = 0;

            // Obtém todas as linhas da tabela, exceto a primeira no tbody (índice 0)
            let linhas = tabela.querySelectorAll("tbody tr");

            /**
             * Pinta todas as linhas de vermelho acima de 10, regra atacado
             * */
            if( linhas.length >= 10 ){
                for (let i = 0; i < linhas.length; i++) {
                    // Adiciona a classe
                    linhas[i].classList.add('linha-vermelha');
                }
            }

            if( linhas.length < 10 ){
            // Itera sobre as linhas
                for (let i = 0; i < linhas.length; i++) {
                    linha = linhas[i];

                    // Obtém a célula (td) que contém o input
                    let celulaQuantidade = linha.getElementsByTagName("td")[2];

                    // Verifica se a célula existe e se contém um input
                    if (celulaQuantidade && celulaQuantidade.querySelector("input")) {
                        // Obtém o valor do input dentro da célula
                        let valorQuantidade = celulaQuantidade.querySelector("input").value;

                        // Converte o valor para número
                        let valorNumerico = parseInt(valorQuantidade);
                        console.log(valorNumerico);
                        // Remove a classe existente
                        linha.classList.remove("linha-vermelha");

                        /**
                         * Pinta vermelho acima de 5 o produto, regra atacado
                         * */
                        if (valorNumerico >= 5) {
                            linha.classList.add('linha-vermelha');
                        }
                    }
                }

            }
        }
    }

    /**
     *
     * */
    function converterFormatoMoeda(valor) {
        // Remove o "R$ " e substitui a vírgula por ponto
        return valor !== "" ? parseFloat(valor.replace("R$ ", "").replace(",", ".")) : 0;
    }

    function isModalAberto() {
        return $('#meuModal').hasClass('show');
    }


    /**
     * Captura o evento do teclado e realiza as ações
     * **/

    document.addEventListener('keydown', function(e) {

        let valorRecebido = 0;
        let valorTroco = 0;
        let abreModal = false;

        //Fechamento da venda F4
        if (e.key === 'F4') {  // 115 é o código da tecla F4
            e.preventDefault();
            valorRecebido = converterFormatoMoeda($("#valorPagar").val());
            console.log(valorRecebido);

            // Verifica se tem modal aberto do fechamento do pagamento
            if (!isModalAberto()) {
                defaultMessageDialog("Selecione a forma de Pagamento!", "question");
                abreModal = false;
                return false;
            }

            //caso tipo seja dinheiro F1
            if ( $('#tipoVenda').val() === "F1" && valorRecebido > 0) {
                valorTroco = valorRecebido - $("#hiddenTotal").val();
                //console.log(valorTroco);
                if (valorTroco < 0) {
                    defaultMessageDialog("Valor recebido inferior ao total da venda!");
                    return;
                }

                // Abre a segunda modal(SALVAR VENDA) ou executa outra ação aqui
                //$('#modal2').modal('show');
                //Livewire.emitTo('cart-component', 'saveSale', $("#codigoVenda").val(), $('#hiddenTotal').val(), $('#tipoVenda').val());

                abreModal = true;

            } else if($('#tipoVenda').val() === "F1" && valorRecebido <= 0) {
                    defaultMessageDialog("Informe o valor recebido da venda?");
                    abreModal = false;
                    return false;
            }
            //caso tipo seja cartão / pix e etc F2,F3
            if ( $('#tipoVenda').val() === "F2" || $('#tipoVenda').val() === "F3") {

                abreModal = true;
            }

            // Abre a segunda modal(SALVAR VENDA) ou executa outra ação aqui
            if(abreModal){
                $('#modal2').modal('show');
                Livewire.emitTo('cart-component', 'saveSale', $("#codigoVenda").val(), $('#hiddenTotal').val(), $('#tipoVenda').val());

                //reseta as informações do produto na tela
                document.getElementById('quantidade').innerText = '';
                document.getElementById('valor_cartao_pix').innerText = '';
                document.getElementById('valor_atacado').innerText = '';
                document.getElementById('valor_parcelado').innerText = '';
                document.getElementById('valor_lista').innerText = '';
                document.getElementById('imagem').innerText = '';
            }

        }

       // Teclas F..
        if (e.key  === 'F1' || e.key  === 'F2' || e.key  === 'F3' ) {
            e.preventDefault();
            console.log(e.key);

            /**
             * Emite para o livewire a açõa de criar a venda do CartComponent
             * CartComponent.php
             * */
            Livewire.emit('createSale');

            /**
             * Pega as informações da Cart
             * */
            Livewire.on('informacoesAtualizadas', (data) => {
                // Manipular os dados recebidos
                console.log('Dados recebidos:', data);

                const isEmpty = data.length === 0 ? true : false;
                if(isEmpty){
                    defaultMessageDialog('Sem vendas a serem finalizadas!');
                }else {
                    <!-- cart.blade.php -->
                    // Faça o que for necessário com os dados, por exemplo, atualizar a interface do usuário
                    $('#botaoAbrirModal').trigger('click');

                    switch (e.key) {
                        case 'F1':
                            options = [{value: '1', text: 'DINHEIRO'}];
                            formaPgto = "DINHEIRO";
                            valorFormatado = formatoMoeda.format(data.totalGeralDinner);
                            valorTotal = data.totalGeralDinner;
                            break;
                        case 'F2':
                            options = [
                                {value: '10', text: 'PIX'}, //pix
                                {value: '9', text: 'DÉBITO'}, //Débito elo
                                {value: '2', text: 'CRÉDITO (1X)'}, //Credito master e visa
                                {value: '4', text: 'CRÉDITO (2X)'} //Parcelado 2x Visa/Master
                            ];
                            formaPgto = "PIX, DÉBITO E CRÉDITO(ATÉ 2X)";
                            valorFormatado = formatoMoeda.format(data.totalGeralPDC);
                            valorTotal = data.totalGeralPDC;
                            break;
                        case 'F3':
                            options = [
                                {value: '5', text: 'CRÉDITO (3X) '}, //Parcelado 3x Visa/Master
                                {value: '6', text: 'CRÉDITO (4X) '}, //Parcelado 4x Visa/Master
                                {value: '7', text: 'CRÉDITO (5X) '}, //Parcelado 5x Visa/Master
                                {value: '8', text: 'CRÉDITO (6X) '} //Parcelado 6x Visa/Master
                            ];
                            formaPgto = "CRÉDITO (3X À 6X)";
                            valorFormatado = formatoMoeda.format(data.totalGeralCredito);
                            valorTotal = data.totalGeralCredito;
                            break;
                    }
                    //Dinheiro
                    if(options.length === 1){
                        $('#divCartao').css('display',  "none");//esconde div froma de pagmamento
                        //$("#divBandeiraCartao").css("display", "none"); //esconde div bandeira
                        $("#divDinner").css("display", "block");//exibe div dinheiro
                        $('#meuModal').on('shown.bs.modal', function () {
                            $('#valorPagar').focus();
                        });

                    }else {
                        // Selecione o elemento select
                        const select = document.getElementById('formaPgto');
                        // Limpe as opções existentes no select
                        select.innerHTML = "<option>Forma de Pagamento</option>";

                        $('#divCartao').css('display',  "block"); //exibe div froma de pagmamento
                        //$("#divBandeiraCartao").css("display", "block"); //exibe div bandeira
                        $("#divDinner").css("display", "none"); //esconde div dinheiro

                        // Adicione as opções ao select
                        options.forEach(opcao => {
                            option = document.createElement('option');
                            option.value = opcao.value;
                            option.text = opcao.text;
                            select.appendChild(option);
                        });
                    }

                    $("#header").html("FORMA DE PAGAMENTO ESCOLHIDA: <p><strong><h2>" + formaPgto + "</h2></strong></p>");

                    /**
                     * Dados do carrinho retornardo pelo Livewire
                     * */
                    // $.each(data, function (index, value) {
                    //     console.log(index + ": " + value.variacao_id);
                    //
                    // });

                    $("#valorTotalVenda").html("<h2>"+valorFormatado+"</h2>");

                    // Verificar se o input já existe
                    const hiddenTotalExistente = document.getElementById('hiddenTotal');
                    const tipoVenda = document.getElementById('tipoVenda');

                    if (hiddenTotalExistente) {
                        // Se existir, apenas atualize o valor
                        hiddenTotalExistente.value = valorTotal;
                        tipoVenda.value = e.key;
                    } else {
                        // Se não existir, crie um novo
                        const hiddenTotal = document.createElement('input');
                        hiddenTotal.value = valorTotal;
                        hiddenTotal.type = "hidden";
                        hiddenTotal.id = "hiddenTotal";
                        $("#modalFecharVenda").append(hiddenTotal);

                        const tipoVenda = document.createElement('input');
                        tipoVenda.value = e.key;
                        tipoVenda.type = "hidden";
                        tipoVenda.id = "tipoVenda";
                        $("#modalFecharVenda").append(tipoVenda);
                    }
                }
            });

            /***
             * Ao digitar o valor no campo valorPagar da modal , calcula o troco
             * */
            // Máscara de moeda para o campo #valorPagar
            $('#valorPagar').inputmask("currency", {
                radixPoint: ",",
                groupSeparator: ".",
                autoGroup: true,
                prefix: 'R$ ',
                rightAlign: false,
                removeMaskOnSubmit: true,
                numericInput: true,
                placeholder: "0"
            });

            /**
             * Ao digitar calcula o troco
             * */
            $("#valorPagar").on('keyup',function() {

                // Obter o valor do campo valorPagar
                const valorPagar = parseFloat($(this).val().replace(/[^\d.,-]/g, '').replace(',', '.')) || 0;

                // Obter o valor total do hiddenTotal
                const valorTotal = parseFloat($("#hiddenTotal").val()) || 0;

                // Calcular o troco
                const troco = valorPagar - valorTotal;

                // Formatando o troco como moeda
                const trocoFormatado = formatoMoeda.format(troco);

                if(troco < 0){
                    $("#troco").css('color', 'red'); // Exemplo: altera a cor do texto para vermelho
                }else{
                    $("#troco").css('color', 'black');
                }
                // Exibir o troco no campo #troco
                $("#troco").val(trocoFormatado);
            });

            // window.livewire.emitTo('cart-component','fecharVenda', (dados) => {
            //     console.log('Dados recebidos:', dados);
            // });

            /*var el1 = document.getElementById('lang')
            var el2 = document.getElementById('body')
            var el3 = document.getElementById('container')


            if(el1.classList.contains('sidebar-noneoverflow')) {

                el1.classList.remove("sidebar-noneoverflow")
                el2.classList.remove("sidebar-noneoverflow")
                el3.classList.remove("sidebar-closed","sbar-open")

            } else {

                el1.classList.add("sidebar-noneoverflow")
                el2.classList.add("sidebar-noneoverflow")
                el3.classList.add("sidebar-closed","sbar-open")
            }*/
        }

    });
    /**
     * Confirmação de exclusão
     * */

    function Confirm(id, eventName, text) {
        Swal.fire({
            title: 'CONFIRMAR',
            text: text,
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Não',
            cancelButtonColor: '#d33',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Sim'
        }).then(function(result) {
           // console.log(eventName, id);
            if (result.value) {
                window.livewire.emit(eventName, id)
               // window.livewire.emitTo('cart-component','eventoDeA', id)
                swal.close()
            }

        });
    }

    /**
     *
     * */
    let defaultMessageDialog  = function(html = 'titulo', icon = 'warning', position = "center"){
        Swal.fire({
            position: position,
            icon: icon,
            html: "<h4>" + html + "</h4>",
            showConfirmButton: false,
            timer: 2500
        });
    }


    // Livewire.on('scan-code-byid', postId => {
    //     Snackbar.show({
    //         text: "OK",
    //         actionText: 'FECHAR',
    //         actionTextColor: '#fff',
    //         backgroundColor: postId == 1 ? '#3b3f5c' : '#e7515a',
    //         pos: 'top-right'
    //     });
    // })
</script>



<script src="{{ asset('plugins/flatpickr/flatpickr.js')}}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
