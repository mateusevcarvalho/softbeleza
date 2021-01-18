<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServicoCategoria;
use Illuminate\Http\Request;

class ServicoCategoriasController extends Controller
{
    public function index(Request $request)
    {
        authorizeAny(['servicos']);
        return response()->json(ServicoCategoria::getResults($request->all()));
    }

    public function store(Request $request)
    {
        authorizeAny(['servicos']);
        if (ServicoCategoria::create($request->all())) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function show($id)
    {
        authorizeAny(['servicos']);
        $categoria = ServicoCategoria::findOrFail($id);
        return response()->json($categoria);
    }

    public function update(Request $request, $id)
    {
        authorizeAny(['servicos']);
        $categoria = ServicoCategoria::findOrFail($id);
        if ($categoria->update($request->all())) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function destroy($id)
    {
        //
    }
}
