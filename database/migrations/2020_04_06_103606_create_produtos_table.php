<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->string('nome');
            $table->double('valor');
            $table->double('desconto_maximo');
            $table->double('desconto_promocional')->nullable();
            $table->text('descricao')->nullable();
            $table->enum('unidade_medida_entrada', ['UN', 'L', 'ML', 'CX']);
            $table->enum('unidade_medida_saida', ['UN', 'L', 'ML', 'CX']);
            $table->integer('quantidade')->default(0)->comment('Baseadona unidade de medida de saída');
            $table->double('valor_unitario');
            $table->double('valor_fracionado');
            $table->double('custo_total')->default(0);
            $table->integer('equivalencia');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
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
        Schema::dropIfExists('produtos');
    }
}
