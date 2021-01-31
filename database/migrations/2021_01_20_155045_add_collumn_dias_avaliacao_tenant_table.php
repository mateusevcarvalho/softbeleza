<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnDiasAvaliacaoTenantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('individuo_id')->nullable()->after('local_id');
            $table->integer('dias_avaliacao')->after('uuid')->default(7);
            $table->string('asaas_client_id')->after('dias_avaliacao')->nullable();
            $table->string('cartao_token')->after('asaas_client_id')->nullable();
            $table->string('dia_vencimento')->after('cartao_token')->nullable();
            $table->enum('tipo_pagamento', ['B', 'C'])->after('cartao_token')->nullable()->comment('Boleto; Cartão');

            $table->foreign('individuo_id')
                ->references('id')
                ->on('individuos')
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

    }
}
