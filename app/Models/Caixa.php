<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caixa extends Model
{
    use SoftDeletes, Search, EstabelecimentoTrait;

    protected $fillable = [
        'usuario_id', 'saldo_inicial', 'saldo_informado_caixa', 'saldo_informado_cartao', 'data_abertura',
        'data_fechamento', 'status'
    ];

    public function comandas()
    {
        return $this->hasMany(Comanda::class)
            ->orderBy('data', 'desc');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class)->withTrashed();
    }
}
