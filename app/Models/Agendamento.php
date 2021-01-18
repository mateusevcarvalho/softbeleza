<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agendamento extends Model
{
    use SoftDeletes, EstabelecimentoTrait, Search;

    protected $fillable = [
        'estabelecimento_id', 'profissional_id', 'servico_id', 'cliente_id', 'inicio', 'fim', 'telefone'
    ];

    public function setTelefoneAttribute($value)
    {
        $this->attributes['telefone'] = so_numero($value);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class)->withTrashed();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }
}
