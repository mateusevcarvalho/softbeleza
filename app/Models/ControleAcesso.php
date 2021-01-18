<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControleAcesso extends Model
{
    protected $fillable = [
        'nome', 'chave'
    ];
}
