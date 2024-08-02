<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasProdutosEntregasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_vendas_produtos_entregas', function (Blueprint $table) {
            $table->id()->autoIncrement();

            $table->unsignedBigInteger('venda_id')->nullable(true);
            $table->foreign('venda_id')->references('id')->on('loja_vendas');

            $table->unsignedBigInteger('forma_id')->nullable(true);
            $table->foreign('forma_id')->references('id')->on('loja_forma_entregas');

            $table->decimal('valor_entrega', 9,2)->default('0.00');

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
        Schema::dropIfExists('loja_vendas_produtos_entregas');
    }
}
