/*
=========================================
|                                       |
|           Scroll To Top               |
|                                       |
=========================================
*/
$('.scrollTop').click(function() {
    $("html, body").animate({scrollTop: 0});
});


$('.navbar .dropdown.notification-dropdown > .dropdown-menu, .navbar .dropdown.message-dropdown > .dropdown-menu ').click(function(e) {
    e.stopPropagation();
});

/*
=========================================
|                                       |
|       Multi-Check checkbox            |
|                                       |
=========================================
*/

function checkall(clickchk, relChkbox) {

    var checker = $('#' + clickchk);
    var multichk = $('.' + relChkbox);


    checker.click(function () {
        multichk.prop('checked', $(this).prop('checked'));
    });
}


/*
=========================================
|                                       |
|           MultiCheck                  |
|                                       |
=========================================
*/

/*
    This MultiCheck Function is recommanded for datatable
*/

function multiCheck(tb_var) {
    tb_var.on("change", ".chk-parent", function() {
        var e=$(this).closest("table").find("td:first-child .child-chk"), a=$(this).is(":checked");
        $(e).each(function() {
            a?($(this).prop("checked", !0), $(this).closest("tr").addClass("active")): ($(this).prop("checked", !1), $(this).closest("tr").removeClass("active"))
        })
    }),
    tb_var.on("change", "tbody tr .new-control", function() {
        $(this).parents("tr").toggleClass("active")
    })
}

/*
=========================================
|                                       |
|           MultiCheck                  |
|                                       |
=========================================
*/

function checkall(clickchk, relChkbox) {

    var checker = $('#' + clickchk);
    var multichk = $('.' + relChkbox);


    checker.click(function () {
        multichk.prop('checked', $(this).prop('checked'));
    });
}

/*
=========================================
|                                       |
|               Tooltips                |
|                                       |
=========================================
*/

$('.bs-tooltip').tooltip();
$('[data-toggle="tooltip"]').tooltip();

/*
=========================================
|                                       |
|               Popovers                |
|                                       |
=========================================
*/

$('.bs-popover').popover();


/*
================================================
|                                              |
|               Rounded Tooltip                |
|                                              |
================================================
*/

$('.t-dot').tooltip({
    template: '<div class="tooltip status rounded-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
})


/*
================================================
|            IE VERSION Dector                 |
================================================
*/

function GetIEVersion() {
  var sAgent = window.navigator.userAgent;
  var Idx = sAgent.indexOf("MSIE");

  // If IE, return version number.
  if (Idx > 0)
    return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));

  // If IE 11 then look for Updated user agent string.
  else if (!!navigator.userAgent.match(/Trident\/7\./))
    return 11;

  else
    return 0; //It is not IE
}

$(function() {
    $("#searchProduct").autocomplete({
        minLength: 2,
        source: function(request, response) {
            $.get('http://127.0.0.1/loja-epos/search', { term: request.term }, function(data) {
                console.log(data);
                // Mapeie os dados para o formato que o autocomplete espera
                const formattedData = data.map(elemento => ({
                    label: elemento.subcodigo +" - "+ elemento.produto_descricao + " - " + elemento.variacao,
                    value: elemento.subcodigo +" - "+ elemento.produto_descricao + " - " + elemento.variacao, // Valor a ser inserido no input quando um item é selecionado
                    subcodigo: elemento.subcodigo,
                    descricao: elemento.produto_descricao + " - " + elemento.variacao,
                    quantidade: elemento.quantidade,
                    valor_cartao_pix: elemento.valor_cartao_pix,
                    valor_atacado:elemento.valor_atacado, //caixa fechada
                    valor_parcelado:elemento.valor_parcelado, //credito
                    valor_lista:elemento.valor_lista, //dinheiro
                    image: elemento.images.length > 0 ? elemento.images[0].path : null
                    // Adicione mais propriedades conforme necessário
                }));

                // Verifique se há dados para exibir
                if (formattedData.length === 0) {
                    formattedData.push({
                        label: 'Nenhum produto encontrado',
                        value: '', // Pode definir como vazio ou outro valor padrão
                        subcodigo: '',
                        descricao: '',
                        quantidade: '',
                        valor_cartao_pix:'',
                        valor_atacado:'',
                        valor_parcelado:'',
                        valor_lista:'',
                        image:'',
                    });
                }

                // Chame a função response com os dados formatados
                response(formattedData);
            });
        },
        select: function(event, ui) {
            Livewire.emit('addToCart',ui.item.subcodigo);
            // Preencha o campo de código com o subcódigo
            $("#codigo").val(ui.item.subcodigo);
            $("#codigoHidden").val(ui.item.subcodigo);

            // Preencha o campo de descrição com o valor selecionado
            $("#descricao").val(ui.item.descricao);

            // Limpe o campo de pesquisa
            $("#searchProduct").val('');

            let quantidade = document.getElementById('quantidade');
            quantidade.innerText = ui.item.quantidade;

            let valor_cartao_pix = document.getElementById('valor_cartao_pix');
            valor_cartao_pix.innerText = parseFloat(ui.item.valor_cartao_pix).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

            let valor_atacado = document.getElementById('valor_atacado');
            valor_atacado.innerText =  parseFloat(ui.item.valor_atacado).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

            let valor_parcelado = document.getElementById('valor_parcelado');
            valor_parcelado.innerText =  parseFloat(ui.item.valor_parcelado).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

            let valor_lista = document.getElementById('valor_lista');
            valor_lista.innerText =  parseFloat(ui.item.valor_lista).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

            let imagem = document.getElementById('imagem');
            imagem.innerHTML = '<img class="custom-imagem" src="http://127.0.0.1/api-loja-new-git/public/storage/produtos/not-image.png">';
            if(ui.item.image){
                imagem.innerHTML = '<img class="custom-imagem" src="http://127.0.0.1/api-loja-new-git/public/storage/' + ui.item.image + '">';
//                console.log(ui.item.image);
            }
            // wire:click.prevent="$emit('scan-code-byid',1)"
            // Retorne false para evitar a ação padrão de preencher o campo de pesquisa com o valor selecionado
            return false;
        }
    });

   /* document.addEventListener('DOMContentLoaded', function () {
        try {
            onScan.attachTo(document,{
               suffixKeyCodes: [13],
                onScan: function (barcode) {
                    console.log(barcode);
                    window.livewire.emit('cart-component','addToCart',barcode);
                }
            });
            window.livewire.emit('scan-ok','Produto Adicionado');
        }catch (e) {
            window.livewire.emit('global-msg', e)
        }
    });*/

// <div id="getting-started"></div>
//     $("#getting-started")
//         .countdown("2023/11/14", function(event) {
//             $(this).text(
//                 event.strftime('%D days %H:%M:%S')
//             );
//         });

});
