document.addEventListener('DOMContentLoaded', function () {
    // Aplicar a máscara ao campo de dinheiro
   //$('#dinheiro').mask('R$ 000.000.000.000.000,00', {reverse: true});
    $('#dinheiro').maskMoney();
    // Atualize o Livewire manualmente quando o valor for alterado
    $('#dinheiro').on('input keydown keypress', function() {
        let valor = $(this).val();
       // console.log( $('#dinheiro').val());
        window.livewire.emit('updatedValorRecebido',  valor);
    });


});


