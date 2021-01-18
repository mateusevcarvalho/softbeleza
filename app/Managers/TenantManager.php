<?php


namespace App\Managers;


use App\Models\Usuario;

class TenantManager
{
    private static $usuario;

    public static function set(Usuario $usuario)
    {
        self::$usuario = $usuario;
    }

    public static function get()
    {
        return self::$usuario;
    }
}
