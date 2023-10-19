<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loja_produtos', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_produto', 25)->unique();
            $table->string('descricao', 255);
            $table->boolean('status')->default(0);
            $table->decimal('valor_produto', 9,2)->default('0.00');

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
        Schema::dropIfExists('loja_produtos');
    }
}
