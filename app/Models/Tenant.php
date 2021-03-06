<?php

namespace App\Models;

use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes, Search;

    protected $fillable = [
        'uuid', 'local_id', 'dias_avaliacao', 'individuo_id', 'asaas_client_id', 'cartao_token', 'dia_vencimento'
    ];

    protected $hidden = [
        'asaas_client_id', 'cartao_token'
    ];

    public function individuo()
    {
        return $this->belongsTo(Individuo::class);
    }

    public function estabelecimentos()
    {
        return $this->hasMany(Estabelecimento::class);
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function servicoCategoria()
    {
        return $this->hasMany(ServicoCategoria::class);
    }

    public function profissionais()
    {
        return $this->hasMany(Profissional::class);
    }
}
