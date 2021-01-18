<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimentacoesEstoque extends Model
{
    protected $fillable = [
        'entrada', 'nome', 'sigla'
    ];

    public function getEntradaAttribute($value)
    {
        return !!$value;
    }
}
