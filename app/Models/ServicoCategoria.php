<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicoCategoria extends Model
{
    use SoftDeletes, EstabelecimentoTrait, Search;

    protected $fillable = [
        'nome', 'estabelecimento_id'
    ];
}
