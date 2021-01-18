<?php

namespace App\Providers;

use App\Models\ControleAcesso;
use App\Models\Usuario;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        try {
            $controleAcessos = ControleAcesso::all();
        } catch (\Exception $e) {
            $controleAcessos = [];
        }

        foreach ($controleAcessos as $controleAcesso) {
            Gate::define($controleAcesso->chave, function (Usuario $usuario) use ($controleAcesso) {
                return Usuario::hasPermission($usuario, $controleAcesso);
            });
        }
    }
}
