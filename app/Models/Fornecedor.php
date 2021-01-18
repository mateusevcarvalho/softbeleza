<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fornecedor extends Model
{
    use SoftDeletes, Search, EstabelecimentoTrait;

    protected $table = 'fornecedores';

    protected $fillable = [
        'individuo_id', 'status'
    ];

    public function individuo()
    {
        return $this->belongsTo(Individuo::class);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ? 'A' : 'I';
    }

    public function getStatusAttribute($value)
    {
        return $value === 'A';
    }
}
