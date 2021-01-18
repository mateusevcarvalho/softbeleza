<?php

namespace App\Scopes;

use App\Managers\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EstabelecimentoScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $usuario = TenantManager::get();
        if ($usuario) {
            $builder->where('tenant_id', $usuario->tenant_id)
                ->where('estabelecimento_id', $usuario->estabelecimento_id);
        }
    }
}
