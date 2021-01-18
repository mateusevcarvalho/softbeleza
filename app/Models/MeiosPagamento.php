<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeiosPagamento extends Model
{
    use SoftDeletes, EstabelecimentoTrait, Search;

    protected $fillable = [
        'estabelecimento_id', 'nome', 'tipo_pagamento', 'max_parcelas', 'taxa_operadora', 'dias_repasse_operadora',
        'forma_pagamento'
    ];
}
