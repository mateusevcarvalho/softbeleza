<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfissionaisServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profissionais_servicos', function (Blueprint $table) {
            $table->unsignedBigInteger('profissional_id');
            $table->unsignedBigInteger('servico_id');
            $table->time('duracao');
            $table->double('valor');
            $table->double('rateio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proficionais_servicos');
    }
}
