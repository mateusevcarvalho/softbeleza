<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndividuosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individuos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_pessoa', ['F', 'J'])->comment('F => Fisica; J => Jurídica');
            $table->string('nome');
            $table->string('razao_social')->nullable();
            $table->string('email_contato')->nullable();
            $table->string('documento')->nullable();
            $table->string('celular', 11)->nullable();
            $table->string('telefone_fixo', 10)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('responsavel')->nullable();
            $table->string('contato_responsavel')->nullable();
            $table->string('apelido')->nullable();
            $table->enum('sexo', ['M', 'F'])->comment('M => Masculino; F => Feminino')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('individuos');
    }
}
