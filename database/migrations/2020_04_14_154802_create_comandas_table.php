<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComandasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->unsignedBigInteger('caixa_id');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->enum('tipo', ['E', 'S', 'R', 'ES'])->default('E')->comment('Entrada; Saque; Reforço; Estorno');
            $table->enum('tipo_desconto_geral', ['D', 'P'])->nullable()->comment('Dinheiro; Porcentagem');
            $table->double('valor_desconto_total')->nullable();
            $table->double('valor_total')->nullable();
            $table->double('valor_pago')->nullable();
            $table->double('valor_pendente')->nullable();
            $table->dateTime('data');
            $table->boolean('estornada')->default(0);
            $table->enum('status', ['A', 'F'])->default('F')->comment('Aberta; Fechada');
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

            $table->foreign('caixa_id')
                ->references('id')
                ->on('caixas')
                ->onDelete('CASCADE');

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
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
        Schema::dropIfExists('comandas');
    }
}
