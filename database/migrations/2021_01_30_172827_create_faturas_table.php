<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('tipo_pagamento', ['B', 'C'])->nullable()->comment('Boleto; Cartão');
            $table->string('transacao_id')->nullable();
            $table->string('bandeira_cartao')->nullable();
            $table->string('final_cartao')->nullable();
            $table->string('url_boleto')->nullable();
            $table->string('status_pagamento')->nullable()
                ->comment('PENDING - Aguardando pagamento; RECEIVED - Recebida (saldo já creditado na conta); CONFIRMED - Pagamento confirmado (saldo ainda não creditado); OVERDUE - Vencida; REFUNDED - Estornada; REFUND_REQUESTED - Estorno Solicitado; AWAITING_RISK_ANALYSIS - Pagamento em análise');

            $table->double('valor');
            $table->boolean('ativo')->default(1);
            $table->timestamps();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
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
        Schema::dropIfExists('faturas');
    }
}
