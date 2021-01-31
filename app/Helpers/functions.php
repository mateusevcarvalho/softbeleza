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

function getControleAcessos($userId)
{
    $usuario = Usuario::with('controleAcessos')->find($userId);
    return $usuario->controleAcessos->toArray();
}

function dias_feriados($ano = null): array
{
    if (empty($ano)) {
        $ano = intval(date('Y'));
    }

    $pascoa = strtotime('+1 day', easter_date($ano)); // Limite de 1970 ou após 2037 da easter_date PHP consulta http://www.php.net/manual/pt_BR/function.easter-date.php
    $dia_pascoa = date('d', $pascoa);
    $mes_pascoa = date('m', $pascoa);
    $ano_pascoa = date('Y', $pascoa);

    $feriados = [
        // Tatas Fixas dos feriados Nacionail Basileiras
        date('Y-m-d', mktime(0, 0, 0, 1, 1, $ano)), // Confraternização Universal - Lei nº 662, de 06/04/49
        date('Y-m-d', mktime(0, 0, 0, 4, 21, $ano)), // Tiradentes - Lei nº 662, de 06/04/49
        date('Y-m-d', mktime(0, 0, 0, 5, 1, $ano)), // Dia do Trabalhador - Lei nº 662, de 06/04/49
        date('Y-m-d', mktime(0, 0, 0, 9, 7, $ano)), // Dia da Independência - Lei nº 662, de 06/04/49
        date('Y-m-d', mktime(0, 0, 0, 10, 12, $ano)), // N. S. Aparecida - Lei nº 6802, de 30/06/80
        date('Y-m-d', mktime(0, 0, 0, 11, 2, $ano)), // Todos os santos - Lei nº 662, de 06/04/49
        date('Y-m-d', mktime(0, 0, 0, 11, 15, $ano)), // Proclamação da republica - Lei nº 662, de 06/04/49
        date('Y-m-d', mktime(0, 0, 0, 12, 25, $ano)), // Natal - Lei nº 662, de 06/04/49

        // Essas Datas dependem diretamente da data de Pascoa
        // mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48, $ano_pascoa), //2ºferia Carnaval
        date('Y-m-d', mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47, $ano_pascoa)), //3ºferia Carnaval
        date('Y-m-d', mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2, $ano_pascoa)), //6ºfeira Santa
        date('Y-m-d', mktime(0, 0, 0, $mes_pascoa, $dia_pascoa, $ano_pascoa)), //Pascoa
        date('Y-m-d', mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60, $ano_pascoa)), //Corpus Cirist

    ];

    sort($feriados);
    return $feriados;
}

function proximo_dia_util($date = null): \Carbon\Carbon
{
    $dataDate = $date ? explode('-', $date) : [];
    $proximoDiaUtil = $date ? \Carbon\Carbon::create($dataDate[0], $dataDate[1], $dataDate[2])->addDay() : now()->addDay();

    while (in_array($proximoDiaUtil->format('Y-m-d'), dias_feriados())) {
        $proximoDiaUtil = $proximoDiaUtil->addDay();
    }

    $numeroDiaSemana = (int)$proximoDiaUtil->format('N'); //1 (para Segunda) até 7 (para Domingo)
    if ($numeroDiaSemana == 6) {
        $proximoDiaUtil = $proximoDiaUtil->addDays(2);
    } else if ($numeroDiaSemana == 7) {
        $proximoDiaUtil = $proximoDiaUtil->addDay();
    }

    return $proximoDiaUtil;
}
