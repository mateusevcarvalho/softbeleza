<?php

namespace App\Models;

use App\Models\Traits\Search;
use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estabelecimento extends Model
{
    use SoftDeletes, TenantTrait, Search;

    protected $fillable = [
        'individuo_id', 'tipos_estabelecimento_id', 'uuid'
    ];

    public function individuo()
    {
        return $this->belongsTo(Individuo::class);
    }
}
