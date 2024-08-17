<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendaProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_vendas_produtos2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id');
            $table->foreign('venda_id')->references('id')->on('loja_vendas');

            $table->string('codigo_produto', 25);
            $table->string('descricao', 255);
            $table->boolean('quantidade');
            $table->decimal('valor_produto',8,2);
            $table->decimal('percentual_desconto',8,2)->default(0);
            $table->boolean('troca')->default(false);

            $table->unsignedBigInteger('fornecedor_id');
            $table->foreign('fornecedor_id')->references('id')->on('loja_fornecedores');

            $table->unsignedBigInteger('categoria_id')->nullable(true);
            $table->foreign('categoria_id')->references('id')->on('loja_categorias');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loja_vendas_produtos');
    }
}
