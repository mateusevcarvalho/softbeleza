<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeiosPagamentosRequest;
use App\Models\MeiosPagamento;
use Illuminate\Http\Request;

class MeiosPagamentosController extends Controller
{
    public function index(Request $request)
    {
        authorizeAny(['configuracao', 'caixa', 'relatorios']);
        return response()->json(MeiosPagamento::getResults($request->all()));
    }

    public function store(MeiosPagamentosRequest $request)
    {
        $this->authorize('configuracao');
        if (MeiosPagamento::create($request->all())) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    public function show($id)
    {
        $this->authorize('configuracao');
        $meioPagamento = MeiosPagamento::findOrFail($id);
        return response()->json($meioPagamento);
    }

    public function update(MeiosPagamentosRequest $request, $id)
    {
        $this->authorize('configuracao');
        $meioPagamento = MeiosPagamento::findOrFail($id);
        if ($meioPagamento->update($request->all())) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    public function destroy($id)
    {
        $this->authorize('configuracao');
        $meioPagamento = MeiosPagamento::findOrFail($id);
        if ($meioPagamento->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }
}
