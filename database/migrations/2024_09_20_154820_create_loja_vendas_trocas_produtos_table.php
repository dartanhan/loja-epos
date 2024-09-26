<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasTrocasProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_vendas_trocas_produtos', function (Blueprint $table) {
            $table->bigInteger('id',true)->unsigned();

            $table->unsignedBigInteger('troca_id')->nullable(true);
            $table->foreign('troca_id')->references('id')->on('loja_vendas_trocas');

            $table->unsignedBigInteger('produto_id')->nullable(true);
            $table->foreign('produto_id')->references('id')->on('loja_produtos_new');

            $table->unsignedBigInteger('tipo_troca_id')->nullable(true);
            $table->foreign('tipo_troca_id')->references('id')->on('loja_tipo_trocas');

            $table->integer('quantidade')->nullable(false);

            $table->decimal('preco',9,2);

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
        Schema::dropIfExists('loja_vendas_trocas_produtos');
    }
}
