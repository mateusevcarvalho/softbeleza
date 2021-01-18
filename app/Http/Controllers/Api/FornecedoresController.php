<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Individuo;
use App\Models\IndividuosEndereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FornecedoresController extends Controller
{

    public function index(Request $request)
    {
        authorizeAny(['fornecedores', 'estoque']);
        return response()->json(Fornecedor::getResults($request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('fornecedores');
        $formData = $request->all();
        $objIndividuo = Individuo::create($formData['individuo']);
        $objIndividuoEndereco = IndividuosEndereco::create(array_merge($formData['individuo']['individuos_endereco'], ['individuo_id' => $objIndividuo->id]));
        $objFornecedor = Fornecedor::create(array_merge($formData, ['individuo_id' => $objIndividuo->id]));
        if ($objIndividuo && $objFornecedor && $objIndividuoEndereco) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('fornecedores');
        $fornecedor = Fornecedor::with('individuo.individuosEndereco')->findOrFail($id);
        return response()->json($fornecedor);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('fornecedores');
        $formData = $request->all();
        $objFornecedor = Fornecedor::with('individuo.individuosEndereco')->findOrFail($id);
        $fornecedor = $objFornecedor->update($formData);
        $objIndividuo = Individuo::find($objFornecedor->individuo_id)->update($formData['individuo']);
        $objIndividuoEndereco = IndividuosEndereco::find($objFornecedor->individuo->individuosEndereco->id)->update($formData['individuo']['individuos_endereco']);
        if ($fornecedor && $objIndividuo && $objIndividuoEndereco) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('fornecedores');
        $fornecedor = Fornecedor::findOrFail($id);
        if ($fornecedor->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }
}
