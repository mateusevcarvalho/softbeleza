<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comanda;
use App\Models\MovimentacoesEstoque;
use App\Models\Produto;
use App\Models\TipoMovimentacoesEstoque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComandasController extends Controller
{
    public function index(Request $request)
    {
        authorizeAny(['caixa', 'relatorios']);
        return response()->json(Comanda::getResults($request->all()));
    }

    public function store(Request $request)
    {
        $this->authorize('caixa');
        DB::beginTransaction();
        $formData = $request->all();
        $comanda = Comanda::create($formData);

        if (isset($formData['comanda_itens'])) {
            $comanda->comandaItens()->createMany($formData['comanda_itens']);
        }

        if (isset($formData['comanda_forma_pagamentos'])) {
            $comanda->comandaFormaPagamentos()->createMany($formData['comanda_forma_pagamentos']);
        }

        Comanda::movimentarEstoque($formData);
        if ($comanda) {
            DB::commit();
            return response()->json(['success' => true]);
        }

        DB::rollBack();
        return response()->json(['success' => false], 500);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('caixa');
        DB::beginTransaction();
        $formData = $request->all();
        $comanda = Comanda::findOrFail($id);

        $comanda->comandaItens()->forceDelete();
        $comanda->comandaFormaPagamentos()->forceDelete();
        $comanda->update($formData);

        if (isset($formData['comanda_itens'])) {
            $comanda->comandaItens()->createMany($formData['comanda_itens']);
        }

        if (isset($formData['comanda_forma_pagamentos'])) {
            $comanda->comandaFormaPagamentos()->createMany($formData['comanda_forma_pagamentos']);
        }

        Comanda::movimentarEstoque($formData);
        if ($comanda) {
            DB::commit();
            return response()->json(['success' => true]);
        }

        DB::rollBack();
        return response()->json(['success' => false], 500);
    }

    public function destroy($id)
    {
        $this->authorize('caixa');
        $comanda = Comanda::findOrFail($id);
        if ($comanda->forceDelete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    public function estornar($id)
    {
        $this->authorize('relatorios');
        DB::beginTransaction();
        Comanda::findOrFail($id)->update(['estornada' => 1]);

        $novaComanda = Comanda::findOrFail($id)->toArray();
        $novaComanda['tipo'] = 'ES';
        $novaComanda['data'] = now();
        Comanda::create($novaComanda);

        $comanda = Comanda::with(['comandaItens'])->findOrFail($id);
        Comanda::movimentarEstoque($comanda->toArray(), 'E-D');

        DB::commit();
        return response()->json(['success' => true]);
    }
}
