<script src="{{URL::asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
<script src="{{URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{URL::asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{URL::asset('assets/js/loader.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>

<script src="{{URL::asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{URL::asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
<script src="{{URL::asset('assets/fontawesome/js/all.min.js')}}"></script>
<script src="{{URL::asset('js/url.js')}}"></script>
<script src="{{URL::asset('plugins/input-mask/jquery.maskMoney.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>
    /****************
     * MENU
     * ****************/

    function openNav() {
        document.getElementById("mySidebar").style.width = "250px";
    }

    function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
    }

    function toggleNav() {
        if (document.getElementById("mySidebar").style.width === "250px") {
            closeNav();
        } else {
            openNav();
        }
    }

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();


    /***
     * PESQUISA DE PRODUTOS
     * */

        $("#searchProduct").autocomplete({
            minLength: 3,
            source: function(request, response) {
                if(request.term.trim()){
                    $.get(fncUrl()+'/search', { term: request.term }, function(data) {
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
                 setTimeout(() => {
                     $("#searchProduct").val('');
                     //Adicona ao focus ao input, de pesqusia de produtos

                 }, 200);
           }

        });


    /**********************
     * *********************
     * *********************/

        // $( '#searchProduct' ).select2( {
        //     theme: 'bootstrap4'
        // } );
    });

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

    /**
    * Formata de moeda para banco
    * */
    function formatarParaDecimal(valor) {
        // Remove qualquer caractere que não seja número ou vírgula
        valor = valor.replace(/[^\d,]/g, '');

        // Substitui a vírgula pelo ponto
        valor = valor.replace(',', '.');

        // Converte para número decimal
        return parseFloat(valor).toFixed(2);
    }

    /**********************
     * Finalizar venda
     * *********************/
    function finalizeSale(codigo_venda) {
        let forma_entrega_id = null;
        const tipo_venda = document.getElementById('tipoVenda');
        const forma_pgto = document.getElementById('formaPgto');
        const forma_entrega = document.getElementById('formaEntrega');
        const dinheiro = document.getElementById('dinheiro');

        //nem sempre vai exister o select de forma de entrega
        if (forma_entrega) {
            forma_entrega_id = forma_entrega.value;
        }

        console.log(codigo_venda, 'tipoVenda', tipo_venda.value,'forma_pgto', forma_pgto.value,'forma_entrega', forma_entrega_id);
        //gero um json
        const data = {'codigo_venda' : codigo_venda,
                        'tipoVenda' : tipo_venda.value,
                        'forma_pgto' : forma_pgto.value,
                        'forma_entrega' : forma_entrega_id,
                        'loja_id' : 2,
                        'valor_dinheiro': parseFloat(formatarParaDecimal(dinheiro.value))
                    };

        //envio para o laravel salvar a venda
        window.livewire.emit('storeSale', data);
    }


    /**
     * Exibe as notificações na aplicação
    * */
    let noty = function(msg, color, icon)
    {
        Snackbar.show({
            text: `<i class="${icon}"></i> ${msg}`,
            actionText: '',
            actionTextColor: '#fff',
            backgroundColor: color,
            pos: 'top-right'
        });
    }
    /**
     * Mensgem padrão para o sistema com sweetalert
     * */
    let message = function(msg, icon,showConfirmButton=false,timer=0)
    {
        Swal.fire({
            icon: icon,
            html: msg,
            showConfirmButton: showConfirmButton,
            timer: timer
        });
    }

    /**
     * Notificações disparadas pelo liveware
     * */
    document.addEventListener('DOMContentLoaded', function() {

        window.livewire.on('global-error', msg => {
            message(msg,'error',true);
        });

        /**
         * Mensagem padrão de sucesso
         *  * */
        window.livewire.on('message', (msg,icon,color,reload=false,focusInput=false) => {
            noty(msg,color,icon);

            if(reload) {
                refresh(msg);
            }
            if(focusInput){
                focusInputSearch();
            }
        });

        window.livewire.on('focus-input-search', msg => {
            document.getElementById('searchProduct').focus();
        });

        /**
         * Caso a venda seja negativa desabilita os btoão de finalizar venda
         * */
        window.livewire.on('btn-finalizar-venda', (acao,valor) => {
            btnFinalizarVenda(acao,valor);
        });

         window.livewire.on('refresh', msg => {
             refresh(msg);
         });

        function refresh(msg){
            if(msg) {
                setTimeout(() => {
                    window.location.reload(msg);
                }, 500);
            }
        }
        /**
         * Informa sobre remocação do cleinte
         * */
        $('.remover-cliente-associado').on('click', function () {
            // console.log('remover-cliente-associado');

            Swal.fire({
                title: 'Tem certeza?',
                text: "Você deseja realmente remover o cliente da venda?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar',
                customClass: {
                    cancelButton: 'btn btn-danger',
                    confirmButton: 'btn btn-primary',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = {
                        user_id :  $(this).data('user-id'),
                        cliente_id :  $(this).data('cliente-id')
                    }
                    window.livewire.emit('removerCliente', data);
                }
            });
        });

    });

    /**
     * CAREEGa o tooltip após o livewire atualizar
     * */
    function activateTooltipsAndFormatting() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    /**
     * Ativa ou desativa o botão de finalizar venda
     * */
    function btnFinalizarVenda(acao,value){
        console.log("btnFinalizarVenda acao >> " + acao, "btnFinalizarVenda value >>" + value);

        const finalizarVendaBtns = document.querySelectorAll('.btn-finalizar-venda');
        const divMsgValorNegativo = document.getElementById('divMsgValorNegativo');

        finalizarVendaBtns.forEach(btn => {
            if (acao) {
                btn.removeAttribute('disabled');
                divMsgValorNegativo.style.display = 'none';
            } else {
                btn.setAttribute('disabled', 'disabled');
                divMsgValorNegativo.style.display = 'block';
            }
        });

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

        /**
         * Ativa desativa o button de finalizar venda no modal quando valor diferente de ""
         * */
        Livewire.on('formaPgtoChanged', (acao,value) => {
            btnFinalizarVenda(acao,value);

            const formaPgtoSelect = document.getElementById('formaPgto');
            const selectedOption = formaPgtoSelect.options[formaPgtoSelect.selectedIndex];
            const slug = selectedOption.getAttribute('data-slug');

           // console.log('acao', acao);
           // console.log('slug', slug);

            const card_dinheiro = document.getElementById('card_dinheiro');
            if (slug === 'dinheiro') {
                card_dinheiro.style.display = 'block';
            } else {
                card_dinheiro.style.display = 'none';
                window.livewire.emit('updatedValorRecebido',0);
            }
            setTimeout(() => {
                document.getElementById('dinheiro').focus();
            }, 200);
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
            $('#slideInModal').modal('hide');
            $('#slideInModalFecharVenda').modal('hide');
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

        $('#openMenu').on('click', function () {
            $('#openMenuModal').modal({
                // backdrop: 'static',  // Disables closing the modal by clicking outside of it
                keyboard: false      // Disables closing the modal with the ESC key
            }).modal('show');
        });

        $('#closeMenuModal').on('click', function () {
            $('#openMenuModal').modal('hide');
        });


    });

    /**
     * Coloca o focus no campo de pesquisa de produtos
     * */
    function focusInputSearch() {
       // console.log("foi");
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


