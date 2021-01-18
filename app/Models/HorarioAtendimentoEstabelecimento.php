<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;

class HorarioAtendimentoEstabelecimento extends Model
{
    use EstabelecimentoTrait, Search;

    protected $fillable = [
        'estabelecimento_id', 'dia', 'hora_inicio', 'hora_fim'
    ];
}
