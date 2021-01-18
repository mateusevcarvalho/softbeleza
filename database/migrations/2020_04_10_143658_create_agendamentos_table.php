<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->unsignedBigInteger('profissional_id');
            $table->unsignedBigInteger('servico_id');
            $table->unsignedBigInteger('cliente_id');
            $table->dateTime('inicio');
            $table->dateTime('fim');
            $table->string('telefone');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('profissional_id')
                ->references('id')
                ->on('profissionais')
                ->onDelete('CASCADE');

            $table->foreign('servico_id')
                ->references('id')
                ->on('servicos')
                ->onDelete('CASCADE');

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('CASCADE');

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
        Schema::dropIfExists('agendamentos');
    }
}
