<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Individuo;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        authorizeAny(['clientes', 'agenda', 'caixa', 'relatorios']);
        return response()->json(Cliente::getResults($request->all()));
    }

    public function store(Request $request)
    {
        authorizeAny(['clientes', 'caixa']);
        $formData = $request->all();
        $objIndividuo = Individuo::create($formData['individuo']);
        $objCliente = Cliente::create(array_merge($formData, ['individuo_id' => $objIndividuo->id]));
        if ($objIndividuo && $objCliente) {
            return response()->json(['success' => true, 'cliente_id' => $objCliente->id]);
        }

        return response()->json(['success' => false], 500);
    }

    public function show($id)
    {
        authorizeAny(['clientes']);
        $cliente = Cliente::with('individuo')->findOrFail($id);
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        authorizeAny(['clientes']);
        $formData = $request->all();
        $objCliente = Cliente::findOrFail($id);
        $cliente = $objCliente->update($formData);
        $objIndividuo = Individuo::find($objCliente->individuo_id)->update($formData['individuo']);
        if ($cliente && $objIndividuo) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    public function destroy($id)
    {
        authorizeAny(['clientes']);
        $cliente = Cliente::findOrFail($id);
        if ($cliente->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }
}
