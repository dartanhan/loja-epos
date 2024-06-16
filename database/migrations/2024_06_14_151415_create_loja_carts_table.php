<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_carts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('cliente_id')->nullable(true);
            $table->foreign('cliente_id')->references('id')->on('loja_clientes');

            $table->unsignedBigInteger('produto_variation_id');
            $table->foreign('produto_variation_id')->references('id')->on('loja_produtos_variacao');

            $table->string('name', 255);
            $table->decimal('price', 9,2)->default('0.00');
            $table->Integer('quantidade')->nullable(false)->default(1);
            $table->string('imagem', 255);
            $table->enum('status',['PAGO','PENDENTE','CANCELADO','ABERTO'])->default('ABERTO');


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
        Schema::dropIfExists('loja_cart');
    }
}
