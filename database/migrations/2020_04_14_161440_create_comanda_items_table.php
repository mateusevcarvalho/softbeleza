<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComandaItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comanda_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->unsignedBigInteger('comanda_id');
            $table->unsignedBigInteger('produto_id')->nullable();
            $table->unsignedBigInteger('servico_id')->nullable();
            $table->unsignedBigInteger('profissional_id')->nullable();
            $table->double('valor');
            $table->integer('quantidade')->nullable();
            $table->enum('unidade_medida', ['UN', 'L', 'ML', 'CX'])->nullable();
            $table->enum('tipo_desconto', ['D', 'P'])->comment('Dinheiro; Porcentagem')->nullable();
            $table->double('rateio')->nullable();
            $table->boolean('descontar_taxas_rateio')->nullable();
            $table->double('valor_desconto')->nullable();
            $table->double('valor_total');
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

            $table->foreign('comanda_id')
                ->references('id')
                ->on('comandas')
                ->onDelete('CASCADE');

            $table->foreign('produto_id')
                ->references('id')
                ->on('produtos')
                ->onDelete('CASCADE');

            $table->foreign('servico_id')
                ->references('id')
                ->on('servicos')
                ->onDelete('CASCADE');

            $table->foreign('profissional_id')
                ->references('id')
                ->on('profissionais')
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
        Schema::dropIfExists('comanda_itens');
    }
}
