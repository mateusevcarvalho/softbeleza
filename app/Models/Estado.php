<?php

namespace App\Models;

use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    Use Search;
    protected $fillable = [
        'nome', 'sigla', 'ibge'
    ];
}
