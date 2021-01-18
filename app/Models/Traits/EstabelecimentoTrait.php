<?php

namespace App\Models\Traits;

use App\Managers\TenantManager;
use App\Models\Estabelecimento;
use App\Models\Tenant;
use App\Scopes\EstabelecimentoScope;
use Illuminate\Database\Eloquent\Model;

trait EstabelecimentoTrait
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstabelecimentoScope());
        static::creating(function (Model $obj) {
            $usuario = TenantManager::get();
            if ($usuario) {
                $obj->tenant_id = $usuario->tenant_id;
                $obj->estabelecimento_id = $usuario->estabelecimento_id;
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }
}
