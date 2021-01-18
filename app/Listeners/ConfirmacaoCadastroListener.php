<?php

namespace App\Listeners;

use App\Events\CadastroSistemaEvent;
use App\Mail\ConfirmacaoCadastroMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ConfirmacaoCadastroListener implements ShouldQueue
{

    public $tries = 5;

    /**
     * Handle the event.
     *
     * @param  CadastroSistemaEvent  $event
     * @return void
     */
    public function handle(CadastroSistemaEvent $event)
    {
        Mail::send(new ConfirmacaoCadastroMail($event->usuarioId));
    }
}
