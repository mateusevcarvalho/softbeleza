<?php

use App\Models\Usuario;

function primeiro_nome($nome)
{
    $nome = explode(' ', $nome);
    return $nome[0];
}

function so_numero($str)
{
    return preg_replace("/[^0-9]/", "", $str);
}

function mascara($val, $mask)
{
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {
        if ($mask[$i] == '#') {
            if (isset($val[$k]))
                $maskared .= $val[$k++];
        } else {
            if (isset($mask[$i]))
                $maskared .= $mask[$i];
        }
    }
    return $maskared;
}

function authorizeAny(array $chave)
{
    if (\Illuminate\Support\Facades\Gate::any($chave)) {
        return;
    }

    abort(403, 'This action is unauthorized.');
}

function getControleAcessos($userId) {
    $usuario = Usuario::with('controleAcessos')->find($userId);
    return $usuario->controleAcessos->toArray();
}
