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
        $url = url('confirmacao/' . $usuario->tenant->uuid);

        return $this->to($usuario->email)
            ->subject('Bem vindo!')
            ->markdown('mail.confirmacao-cadastro', compact('usuario', 'primeiroNome', 'url'));
    }
}
