<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndividuosEndereco extends Model
{
    protected $fillable = [
        'individuo_id', 'estado_id', 'cidade_id', 'cep', 'logradouro', 'bairro', 'numero', 'complemento'
    ];

    public function individuo()
    {
        return $this->belongsTo(Individuo::class);
    }
}
