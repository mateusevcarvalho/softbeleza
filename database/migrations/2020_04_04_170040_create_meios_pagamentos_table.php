<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeiosPagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meios_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->string('nome');
            $table->enum('tipo_pagamento', ['V', 'P'])->comment('V => Á vista; P => Á prazo');
            $table->enum('forma_pagamento', ['D', 'C'])->comment('D => Dinheiro; C => Cartão');
            $table->integer('max_parcelas')->nullable();
            $table->double('taxa_operadora');
            $table->integer('dias_repasse_operadora')->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meios_pagamentos');
    }
}
