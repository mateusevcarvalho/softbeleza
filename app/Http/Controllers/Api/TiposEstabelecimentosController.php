<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TiposEstabelecimento;
use Illuminate\Http\Request;

class TiposEstabelecimentosController extends Controller
{
    public function index()
    {
        $tipos = TiposEstabelecimento::all();
        return response()->json($tipos);
    }
}
