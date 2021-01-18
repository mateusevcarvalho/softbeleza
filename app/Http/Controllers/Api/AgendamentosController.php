<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use Illuminate\Http\Request;

class AgendamentosController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('agenda');
        return response()->json(Agendamento::getResults($request->all()));
    }

    public function store(Request $request)
    {
        $this->authorize('agenda');
        $formData = $request->all();
        $agendamentos = Agendamento::where('profissional_id', $formData['profissional_id'])
            ->where([['inicio', '>=', $formData['inicio']], ['inicio', '<', $formData['fim']]])
            ->orWhere([['fim', '>', $formData['inicio']], ['fim', '<', $formData['fim']]])
            ->get()->count();

        if (!!$agendamentos) {
            return response()->json(['success' => false], 400);
        }

        if (Agendamento::create($formData)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false,], 500);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('agenda');
        $agendamento = Agendamento::findOrFail($id);
        $formData = $request->all();

        $agendamentos = Agendamento::where('profissional_id', $formData['profissional_id'])
            ->where('id', '!=', $id)
            ->where([['inicio', '>=', $formData['inicio']], ['inicio', '<', $formData['fim']]])
            ->orWhere([['fim', '>', $formData['inicio']], ['fim', '<', $formData['fim']]])
            ->get()->count();

        if (!!$agendamentos) {
            return response()->json(['success' => false], 400);
        }

        if ($agendamento->update($request->all())) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false,], 500);
    }

    public function destroy($id)
    {
        $this->authorize('agenda');
        $agendamento = Agendamento::findOrFail($id);
        if ($agendamento->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false,], 500);
    }
}
