<?php

namespace App\Models;

use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use Search;
    protected $fillable = [
        'nome', 'ibge', 'estado_id'
    ];
}
