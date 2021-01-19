@component('mail::message')
# Olá {{primeiro_nome($usuario->individuo->nome)}}!

## Bem Vindo(a) ao sistema SoftBeleza!

A partir de hoje você pode acessar ao sistema com todas funcionalidades disponíveis  economizando horas de trabalho,
clicando no botão logo abaixo.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Confirmar Cadastro
@endcomponent

Att,<br>
SoftBeleza

### contato@softbeleza.com.br|(31) 99124-3832
@endcomponent
