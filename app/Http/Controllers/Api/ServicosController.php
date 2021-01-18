<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServicosRequest;
use App\Models\Servico;
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;

class ServicosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        authorizeAny(['servicos', 'agenda', 'caixa', 'profissionais', 'relatorios']);
        return Servico::getResults($request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServicosRequest $request)
    {
        authorizeAny(['servicos']);
        $formData = $request->all();
        if ($formData['possui_desconto']) {
            $formData['data_desconto_inicio'] = $formData['data_desconto_inicio'] . ' ' . $formData['hora_inicio'];
            $formData['data_desconto_fim'] = $formData['data_desconto_fim'] . ' ' . $formData['hora_fim'];
        }

        if (Servico::create($formData)) {
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
        authorizeAny(['servicos']);
        $servico = Servico::findOrFail($id);
        return response()->json($servico);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServicosRequest $request, $id)
    {
        authorizeAny(['servicos']);
        $formData = $request->all();
        $servico = Servico::findOrFail($id);
        if ($formData['possui_desconto']) {
            $formData['data_desconto_inicio'] = $formData['data_desconto_inicio'] . ' ' . $formData['hora_inicio'];
            $formData['data_desconto_fim'] = $formData['data_desconto_fim'] . ' ' . $formData['hora_fim'];
        }

        if ($servico->update($formData)) {
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
        authorizeAny(['servicos']);
        $servico = Servico::findOrFail($id);
        if ($servico->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }
}
