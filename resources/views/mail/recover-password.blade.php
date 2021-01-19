@component('mail::message')
# Olá {{primeiro_nome($usuario->individuo->nome)}}!

Alguém solicitou que a senha fosse redefinida.

Se isso foi um engano, apenas ignore este e-mail e nada acontecerá.

Para redefinir sua senha, clique no botão abaixo:
@component('mail::button', ['url' => $url, 'color' => 'primary'])
Recuperar Senha
@endcomponent

Att,<br>
SoftBeleza

### contato@softbeleza.com.br|(31) 99124-3832
@endcomponent
