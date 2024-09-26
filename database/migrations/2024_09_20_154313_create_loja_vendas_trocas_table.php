<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaVendasTrocasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_vendas_trocas', function (Blueprint $table) {
            $table->bigInteger('id',true)->unsigned();

            $table->unsignedBigInteger('venda_id')->nullable(true)->comment('Id da venda original');
            $table->foreign('venda_id')->references('id')->on('loja_vendas');

            $table->unsignedBigInteger('status_id')->nullable(true)->comment('Id do status da troca');
            $table->foreign('status_id')->references('id')->on('loja_status_trocas');

            $table->timestamp('data_troca');

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
        Schema::dropIfExists('loja_vendas_trocas');
    }
}
