<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Individuo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tipo_pessoa', 'nome', 'razao_social', 'email_contato', 'documento', 'celular', 'telefone_fixo',
        'data_nascimento', 'sexo', 'responsavel', 'contato_responsavel', 'apelido'
    ];

    public function individuosEndereco()
    {
        return $this->hasOne(IndividuosEndereco::class);
    }

    public function setDocumentoAttribute($value)
    {
        $this->attributes['documento'] = so_numero($value);
    }

    public function setCelularAttribute($value)
    {
        $this->attributes['celular'] = so_numero($value);
    }

    public function setTelefoneFixoAttribute($value)
    {
        $this->attributes['telefone_fixo'] = so_numero($value);
    }

    public function setContatoResponsavelAttribute($value)
    {
        $this->attributes['contato_responsavel'] = so_numero($value);
    }
}
