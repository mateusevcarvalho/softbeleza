<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstabelecimentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estabelecimentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('individuo_id');
            $table->unsignedBigInteger('tipos_estabelecimento_id');
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('individuo_id')
                ->references('id')
                ->on('individuos')
                ->onDelete('CASCADE');

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('CASCADE');

            $table->foreign('tipos_estabelecimento_id')
                ->references('id')
                ->on('tipos_estabelecimentos')
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
        Schema::dropIfExists('estabelecimentos');
    }
}
