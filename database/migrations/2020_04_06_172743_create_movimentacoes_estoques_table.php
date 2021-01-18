<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimentacoesEstoquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimentacoes_estoques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->unsignedBigInteger('tipo_movimentacoes_estoque_id');
            $table->unsignedBigInteger('fornecedor_id')->nullable();
            $table->unsignedBigInteger('profissional_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('nota_fiscal')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('CASCADE');

            $table->foreign('profissional_id')
                ->references('id')
                ->on('profissionais')
                ->onDelete('CASCADE');

            $table->foreign('tipo_movimentacoes_estoque_id')
                ->references('id')
                ->on('tipo_movimentacoes_estoques')
                ->onDelete('CASCADE');

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('CASCADE');

            $table->foreign('fornecedor_id')
                ->references('id')
                ->on('fornecedores')
                ->onDelete('CASCADE');

            $table->foreign('estabelecimento_id')
                ->references('id')
                ->on('estabelecimentos')
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
        Schema::dropIfExists('movimentacoes_estoques');
    }
}
