<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('individuo_id');
            $table->unsignedBigInteger('estabelecimento_id')->nullable();
            $table->unsignedBigInteger('profissional_id')->nullable();
            $table->uuid('uuid');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->enum('status', ['A', 'I'])->default('A')->comment('A => Ativo; I => Inativo');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('profissional_id')
                ->references('id')
                ->on('profissionais')
                ->onDelete('CASCADE');

            $table->foreign('individuo_id')
                ->references('id')
                ->on('individuos')
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
        Schema::dropIfExists('usuarios');
    }
}
