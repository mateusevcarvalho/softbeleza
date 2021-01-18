<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servico extends Model
{
    use SoftDeletes, EstabelecimentoTrait, Search;

    protected $fillable = [
        'estabelecimento_id', 'codigo', 'nome', 'duracao', 'valor', 'rateio', 'descricao', 'possui_desconto',
        'data_desconto_inicio', 'data_desconto_fim', 'valor_desconto', 'agenda_online', 'status', 'valor_sob_consulta',
        'servico_categoria_id'
    ];

    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ? 'A' : 'I';
    }

    public function getStatusAttribute($value)
    {
        return $value === 'A';
    }

    public function getPossuiDescontoAttribute($value)
    {
        return !!$value;
    }

    public function getAgendaOnlineAttribute($value)
    {
        return !!$value;
    }

    public function getValorSobConsultaAttribute($value)
    {
        return !!$value;
    }
}
