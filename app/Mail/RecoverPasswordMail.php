<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoverPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 5;
    private $url;
    private $usuario;

    /**
     * Create a new message instance.
     *
     * @param $url
     * @param $usuario
     */
    public function __construct($url, $usuario)
    {
        $this->url = $url;
        $this->usuario = $usuario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->usuario->email)
            ->subject('Recuperação de Senha')
            ->markdown('mail.recover-password', ['usuario' => $this->usuario, 'url' => $this->url]);
    }
}
