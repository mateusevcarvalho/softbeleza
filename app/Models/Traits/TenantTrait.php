<?php


namespace App\Models\Traits;


use App\Managers\TenantManager;
use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait TenantTrait
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope());
        static::creating(function (Model $obj) {
            $usuario = TenantManager::get();
            if ($usuario)
                $obj->tenant_id = $usuario->tenant_id;
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
