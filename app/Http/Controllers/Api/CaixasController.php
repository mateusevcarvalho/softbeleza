<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Managers\TenantManager;
use App\Models\Caixa;
use Illuminate\Http\Request;

class CaixasController extends Controller
{
    public function index(Request $request)
    {
        authorizeAny(['caixa']);
        return response()->json(Caixa::getResults($request->all()));
    }

    public function store(Request $request)
    {
        authorizeAny(['caixa', 'relatorios']);
        $formData = $request->all();
        $formData['usuario_id'] = TenantManager::get()->id;
        $formData['data_abertura'] = now();

        if (Caixa::create($formData)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    public function update(Request $request, $id)
    {
        authorizeAny(['caixa']);
        $formData = $request->all();
        $caixa = Caixa::findOrFail($id);

        if ($caixa->update($formData)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    public function reabrir($id)
    {
        authorizeAny(['caixa']);
        $caixa = Caixa::findOrFail($id);
        $caixaAberto = Caixa::where([
            ['status', 'A'],
            ['usuario_id', $caixa->usuario_id]
        ])->get();

        if (!count($caixaAberto)) {
            $caixa->status = 'A';
            $caixa->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
    }
}
