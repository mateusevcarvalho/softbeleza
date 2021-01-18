<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndividuosEnderecosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individuos_enderecos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('individuo_id');
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable();
            $table->string('cep')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('bairro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->timestamps();

            $table->foreign('individuo_id')
                ->references('id')
                ->on('individuos')
                ->onDelete('CASCADE');

            $table->foreign('estado_id')
                ->references('id')
                ->on('estados')
                ->onDelete('CASCADE');

            $table->foreign('cidade_id')
                ->references('id')
                ->on('cidades')
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
        Schema::dropIfExists('individuos_enderecos');
    }
}
