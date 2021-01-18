<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use EstabelecimentoTrait, SoftDeletes, Search;

    protected $fillable = [
        'individuo_id', 'estabelecimento_id', 'nota'
    ];

    public function individuo()
    {
        return $this->belongsTo(Individuo::class);
    }

    public function comandas()
    {
        return $this->hasMany(Comanda::class)
            ->orderBy('data', 'DESC');
    }
}
