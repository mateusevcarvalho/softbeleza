<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComandaFormaPagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comanda_forma_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('estabelecimento_id');
            $table->unsignedBigInteger('comanda_id');
            $table->unsignedBigInteger('meios_pagamento_id');
            $table->double('taxa')->nullable();
            $table->double('valor');
            $table->integer('parcelas')->nullable();
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

            $table->foreign('comanda_id')
                ->references('id')
                ->on('comandas')
                ->onDelete('CASCADE');

            $table->foreign('meios_pagamento_id')
                ->references('id')
                ->on('meios_pagamentos')
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
        Schema::dropIfExists('comanda_forma_pagamentos');
    }
}
