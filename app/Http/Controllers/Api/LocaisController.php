<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Local;
use Illuminate\Http\Request;

class LocaisController extends Controller
{
    public function index()
    {
        $locais = Local::all();
        return response()->json($locais);
    }

    public function cidades(Request $request)
    {
        return response()->json(Cidade::getResults($request->all()));
    }

    public function estados(Request $request)
    {
        return response()->json(Estado::getResults($request->all()));
    }
}
