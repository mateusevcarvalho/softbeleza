<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmacaoCadastroMail extends Mailable
{
    use Queueable, SerializesModels;
    public $usuarioId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuarioId)
    {
        $this->usuarioId = $usuarioId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $usuario = Usuario::with(['individuo', 'tenant'])->findOrFail($this->usuarioId);
        $primeiroNome = primeiro_nome($usuario->individuo->nome);
        $linkConfirmacaoCadastro = url('confirmacao/' . $usuario->tenant->uuid);

        $this->subject('Bem vindo!');
        $this->to($usuario->email, $usuario->individuo->nome);

        return $this->view('mail.confirmacao-cadastro', compact('usuario', 'primeiroNome',
            'linkConfirmacaoCadastro'));
    }
}
