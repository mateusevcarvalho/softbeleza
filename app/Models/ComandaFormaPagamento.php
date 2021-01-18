<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComandaFormaPagamento extends Model
{
    use EstabelecimentoTrait, SoftDeletes, Search;

    protected $fillable = [
        'comanda_id', 'meios_pagamento_id', 'valor', 'parcelas', 'taxa'
    ];

    public function meiosPagamento()
    {
        return $this->belongsTo(MeiosPagamento::class);
    }
}
