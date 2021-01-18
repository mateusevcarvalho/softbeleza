<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;

class HorarioAtendimentoProfissional extends Model
{
    use EstabelecimentoTrait, Search;

    protected $table = 'horario_atendimento_profissionais';

    protected $fillable = [
        'estabelecimento_id', 'dia', 'hora_inicio', 'hora_fim', 'profissional_id'
    ];
}
