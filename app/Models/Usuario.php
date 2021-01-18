<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, EstabelecimentoTrait, Search;

    protected $fillable = [
        'email', 'status', 'estabelecimento_id', 'individuo_id', 'password', 'profissional_id', 'uuid'
    ];

    protected $hidden = [
        'password'
    ];

    public function individuo()
    {
        return $this->belongsTo(Individuo::class);
    }

    public function controleAcessos()
    {
        return $this->belongsToMany(ControleAcesso::class, 'usuarios_controle_acessos');
    }

    public static function hasPermission(Usuario $usuario, ControleAcesso $controleAcesso)
    {
        foreach ($usuario->controleAcessos as $usuarioControleAcesso) {
            if ($usuarioControleAcesso->id === $controleAcesso->id
                || $usuarioControleAcesso->chave === 'administrativo') {
                return true;
            }
        }

        return false;
    }
}
