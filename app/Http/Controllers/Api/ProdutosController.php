<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    public function index(Request $request)
    {
        authorizeAny(['produtos', 'caixa', 'estoque', 'relatorios']);
        return response()->json(Produto::getResults($request->all()));
    }

    public function store(Request $request)
    {
        authorizeAny(['produtos', 'estoque']);
        if (Produto::create($request->all())) {
            return response()->json(['success', true]);
        }

        return response()->json(['success', false], 500);
    }

    public function show($id)
    {
        authorizeAny(['produtos', 'estoque']);
        $produto = Produto::findOrFail($id);
        return response()->json($produto);
    }

    public function update(Request $request, $id)
    {
        authorizeAny(['produtos', 'estoque']);
        $produto = Produto::findOrFail($id);
        if ($produto->update($request->all())) {
            return response()->json(['success', true]);
        }

        return response()->json(['success', false], 500);
    }

    public function destroy($id)
    {
        authorizeAny(['produtos', 'estoque']);
        $produto = Produto::findOrFail($id);
        if ($produto->delete()) {
            return response()->json(['success', true]);
        }

        return response()->json(['success', false], 500);
    }
}
