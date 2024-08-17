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

let selectedOptions;
let ordemSelecionada = [];
let pagamentos = [];

$(document).ready(function() {
    // $('[data-toggle="tooltip"]').tooltip();

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

function totalVenda() {
    let valorTotal = $('.total-value.total-card').text();
    return parseFloat(valorTotal.replace('R$', '').trim().replace(',', '.'));
}
/**********************
 * Finalizar venda
 * *********************/
function finalizeSale(codigo_venda) {
    let forma_entrega_id = null;
    const tipo_venda = document.getElementById('tipoVenda');
    const loja_id = document.getElementById('loja_id');
    const forma_entrega = document.getElementById('formaEntrega');
    const dinheiro = document.getElementById('dinheiro');

    //nem sempre vai exister o select de forma de entrega
    if (forma_entrega) {
        forma_entrega_id = forma_entrega.value;
    }

    pagamentos = [];
    let todosPreenchidos = true;
    selectedOptions.each(function () {
        let slug = $(this).data('slug');
        let text = $(this).data('text');
        let valor =  $(`#${slug}`).val();
        let forma_id = $(`#${slug}`).data('id');//quando é forma dupla de pagamento pega dos inputs criados
        let paymentMethod = $(this).val(); //quando é uma unica forma de pagmento pegta os dados do select

        // Valida se o campo está preenchido
        if (!valor && selectedOptions.length >= 2 ) {
            todosPreenchidos = false; // Marca como falso se qualquer campo estiver vazio
            //alert(`O campo referente a ${slug} precisa ser preenchido.`); // Exibe alerta específico para o campo não preenchido
            noty(`O campo referente a ${text} precisa ser preenchido.`,'red','fas fa-exclamation-circle');
            $(`#${slug}`).css('border', '1px solid red');
            return false; // Sai do loop early, pois já encontrou um campo vazio
        }else {
            $(`#${slug}`).css('border', '');
        }
       // console.log('selectedOptions.length : ',selectedOptions.length);
        //venda dupla
        if (selectedOptions.length >= 2) {
            pagamentos.push({
                forma_pagamento: slug, // Identificador da forma de pagamento
                valor: parseFloat(formatarParaDecimal(valor)), // Valor já convertido para decimal
                id:forma_id
            });
        }else{
            pagamentos.push({
                forma_pagamento: slug, // Identificador da forma de pagamento
                valor: totalVenda(), // Valor já convertido para decimal
                id:parseInt(paymentMethod)
            });
        }
    });

    // console.log(codigo_venda, 'tipoVenda', tipo_venda.value,'forma_pgto', forma_pgto.value,'forma_entrega', forma_entrega_id);
    //gero um json
    const data = {'codigo_venda' : codigo_venda,
        'tipoVenda' : parseInt(tipo_venda.value),
        'forma_pgto' : pagamentos,
        'forma_entrega' : forma_entrega_id,
        'loja_id' : parseInt(loja_id.value),
        'valor_dinheiro': dinheiro.value !== '' ? parseFloat(formatarParaDecimal(dinheiro.value)) : 0
    };
// console.log(data);
// return;
    if(todosPreenchidos){
        message("Aguarde! Finalizando a venda!", "info");
        //envio para o laravel salvar a venda
        window.livewire.emit('storeSale', data);
    }
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
     * Mensagem padrão
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
                window.location.reload();
            }, 600);
        }
    }
    /**
     * Informa sobre remocação do cliente
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

    /**
     * Ao mudar a forma d epagmaento emit um evento o livewire
     * */
    $('.chosen-tipo-venda').on('change', function () {
        console.log('chosen-tipo-venda');
        window.livewire.emitTo('tipo-venda', 'tipoUpdated',$(this).val());
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
let finalizarVendaBtns = null;
let divMsgValorNegativo = null
let card_venda_dupla = null;
let card_dinheiro = null;

function btnFinalizarVenda(acao,value){
     console.log("btnFinalizarVenda acao >> " + acao, "btnFinalizarVenda value >>" + value);

     finalizarVendaBtns = document.querySelectorAll('.btn-finalizar-venda');
     divMsgValorNegativo = document.getElementById('divMsgValorNegativo');

    finalizarVendaBtns.forEach(btn => {
        if (!acao) {
            btn.setAttribute('disabled', 'disabled');
            divMsgValorNegativo.style.display = 'block';
        }

        if(acao){
            if(value > 0) {
                btn.removeAttribute('disabled');
                divMsgValorNegativo.style.display = 'none';
            }else{

                btn.setAttribute('disabled', 'disabled');
                divMsgValorNegativo.style.display = 'none';
            }
        }
    });
}

/**
 * Ativa desativa o button de finalizar venda no modal quando valor diferente de ""
 * */
// let allPaymentMethods = null;
//Livewire.on('formaPgtoChanged', (acao,value) => {}

$('#formaPgto').on('change', function() {
    ordemSelecionada = [];
    selectedOptions = '';
    card_venda_dupla = document.getElementById('card_venda_dupla');
    card_dinheiro = document.getElementById('card_dinheiro');

    // Obtém o valor do item selecionado
    selectedOptions = $(this).find(':selected');
    //ao selecionar apenas uma forma de pagamento , abilita os botões
    btnFinalizarVenda(true, 1);

    // Limpa o container antes de adicionar novos inputs e reseta a ordem de seleção
    $('#paymentInputsContainer').empty();

    // let valorTotal = $('.total-value.total-card').text();
    // let valorTotalVenda = parseFloat(valorTotal.replace('R$', '').trim().replace(',', '.'));
    console.log('selectedOptions.length', selectedOptions.length);
    if (selectedOptions.length === 0) {
        btnFinalizarVenda(true, 0);
        card_dinheiro.style.display = 'none';
        card_venda_dupla.style.display = 'none';
    }

    // Verifica se há exatamente 2 opções selecionadas
    if (selectedOptions.length === 2) {
        card_venda_dupla.style.display = 'block';
        card_dinheiro.style.display = 'none';

        selectedOptions.each(function () {
            let slug = $(this).data('slug');
            let texto = $(this).data('text');
            let paymentMethod = $(this).val();

            // Adiciona o slug ao array de ordem, se ainda não estiver presente
            if (!ordemSelecionada.includes(slug)) {
                ordemSelecionada.push({ slug, texto, paymentMethod });
            }
        });

        // Renderiza os inputs na ordem correta
        ordemSelecionada.forEach(function (item) {
            let inputHtml = `
                    <div class="payment-input mb-1">
                        <label for="input-${item.slug}">${item.texto}</label>
                        <input type="text" id="${item.slug}" name="${item.slug}" data-id="${item.paymentMethod}"
                        placeholder="${item.texto}" aria-label="${item.texto}" aria-describedby="${item.texto}"
                        data-prefix="R$ " data-thousands="." data-decimal=","
                        class="form-control">
                    </div>
                `;
            $('#paymentInputsContainer').append(inputHtml);
        });

        // Configura eventos e máscaras para os inputs gerados
        ordemSelecionada.forEach(function (item) {
            $(`#${item.slug}`).maskMoney();
            $(`#${item.slug}`).on('input keydown keypress', function() {
                let valor = $(this).val();
                atualizarValores(item.slug, valor, totalVenda());
            });

            // Deixa o primeiro campo habilitado e os demais desabilitados
             if (ordemSelecionada[0].slug === item.slug) {
                // $(`#${item.slug}`).prop('disabled', false);
                 //$(`#${item.slug}`).focus();
                 setTimeout(() => {
                     $(`#${item.slug}`).focus();
                 }, 200);
             }
             //else {
            //     $(`#${item.slug}`).prop('disabled', true);
            // }
        });
    }

    // Caso tenha apenas uma opção selecionada e seja "dinheiro"
    if (selectedOptions.length === 1 && selectedOptions[0].dataset.slug === 'dinheiro') {
        card_dinheiro.style.display = 'block';
        card_venda_dupla.style.display = 'none';
        window.livewire.emit('updatedValorRecebido', 0);
        setTimeout(() => {
            document.getElementById('dinheiro').focus();
        }, 200);
    }

    // Caso tenha apenas uma opção selecionada diferente
    if (selectedOptions.length === 1) {
        card_venda_dupla.style.display = 'none';
    }
});

function atualizarValores(slugAtual, valor, valorTotalVenda) {
    let valorDigitado = parseFloat(valor.replace('R$', '').trim().replace(',', '.')) || 0;
    let valorRestante = valorTotalVenda - valorDigitado;
    divMsgValorNegativo = document.getElementById('divMsgValorNegativo');

    ordemSelecionada.forEach(function (item) {
        if (item.slug !== slugAtual) {
            $(`#${item.slug}`).val(formatarDecimal(valorRestante));//.prop('readonly', true);
        }
    });

    finalizarVendaBtns = document.querySelectorAll('.btn-finalizar-venda');
    finalizarVendaBtns.forEach(btn => {
        if(valorDigitado > 0){
            btn.removeAttribute('disabled');

            if(parseFloat(valorRestante.toString()) < 0){
                divMsgValorNegativo.style.display = 'block';
                btn.setAttribute('disabled', 'disabled');
            }else{
                divMsgValorNegativo.style.display = 'none';
            }
        }
        else{
            btn.setAttribute('disabled', 'disabled');
        }
    });
}

function formatarDecimal(valor) {
    return `R$ ${valor.toFixed(2).replace('.', ',')}`;
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
        window.livewire.emitTo('sale','loadSales'); //atualiza os dados do modal

        setTimeout(() => {
            //allPaymentMethods = getAllPaymentMethods();
            $('.chosen-tipo-venda').chosen();
            $('.chosen-forma-entrega').chosen();
            $('.chosen-select').chosen(
                {
                        max_selected_options: 2,
                        width: "100%"
                        });
        }, 1000);
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
