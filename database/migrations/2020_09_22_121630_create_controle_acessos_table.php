<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControleAcessosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controle_acessos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('chave');
            $table->timestamps();
        });

        Schema::create('usuarios_controle_acessos', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('controle_acesso_id');

            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios')
                ->onDelete('CASCADE');

            $table->foreign('controle_acesso_id')
                ->references('id')
                ->on('controle_acessos')
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
        Schema::dropIfExists('usuarios_controle_acessos');
        Schema::dropIfExists('controle_acessos');
    }
}
