<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profissional extends Model
{
    use SoftDeletes, EstabelecimentoTrait, Search;

    protected $table = 'profissionais';

    protected $fillable = [
        'estabelecimento_id', 'individuo_id', 'descontar_taxas_rateio', 'avatar', 'status',
        'link_instagram', 'link_facebook', 'link_twitter', 'possui_agenda', 'sobre'
    ];

    public function horarioAtendimentoProfissional()
    {
        return $this->hasMany(HorarioAtendimentoProfissional::class);
    }

    public function individuo()
    {
        return $this->belongsTo(Individuo::class);
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class);
    }

    public function servicosRelatorio()
    {
        return $this->belongsToMany(Servico::class, 'profissionais_servicos',
            'profissional_id', 'servico_id')
            ->withPivot('duracao', 'valor', 'rateio');
    }

    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'profissionais_servicos',
            'profissional_id', 'servico_id')
            ->withPivot('duracao', 'valor', 'rateio')
            ->where('status', 'A');
    }

    public function getDescontarTaxasRateioAttribute($value)
    {
        return !!$value;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ? 'A' : 'I';
    }

    public function getStatusAttribute($value)
    {
        return $value === 'A';
    }

    public function getPossuiAgendaAttribute($value)
    {
        return !!$value;
    }

}
