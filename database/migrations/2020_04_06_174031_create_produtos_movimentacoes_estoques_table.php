<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutosMovimentacoesEstoquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos_movimentacoes_estoques', function (Blueprint $table) {
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('movimentacoes_estoque_id');
            $table->integer('quantidade');
            $table->enum('unidade_medida', ['UN', 'L', 'ML', 'CX']);
            $table->double('valor_unitario');
            $table->double('quantidade_total');
            $table->double('custo_total');
            $table->double('saldo_custo_total');

            $table->foreign('produto_id')
                ->references('id')
                ->on('produtos')
                ->onDelete('CASCADE');

            $table->foreign('movimentacoes_estoque_id')
                ->references('id')
                ->on('movimentacoes_estoques')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos_movimentacoes_estoques');
    }
}
