<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'data_vencimento', 'data_pagamento', 'tipo_pagamento', 'transacao_id', 'status_pagamento',
        'bandeira_cartao', 'url_boleto', 'final_cartao', 'ativo', 'valor'
    ];

}
