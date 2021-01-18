<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->unsignedBigInteger('servico_categoria_id');
            $table->string('codigo');
            $table->string('nome');
            $table->time('duracao');
            $table->double('valor');
            $table->double('rateio')->nullable();
            $table->mediumText('descricao')->nullable();
            $table->boolean('possui_desconto');
            $table->dateTime('data_desconto_inicio')->nullable();
            $table->dateTime('data_desconto_fim')->nullable();
            $table->double('valor_desconto')->nullable();
            $table->boolean('agenda_online');
            $table->boolean('valor_sob_consulta');
            $table->enum('status', ['A', 'I'])->default('A')->comment('A => Ativo; I => Inativo');
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

            $table->foreign('servico_categoria_id')
                ->references('id')
                ->on('servico_categorias')
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
        Schema::dropIfExists('servicos');
    }
}
