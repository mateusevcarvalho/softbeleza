<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estabelecimento;
use App\Models\HorarioAtendimentoEstabelecimento;
use App\Models\Individuo;
use App\Models\IndividuosEndereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstabelecimentosController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('configuracao');
        return response()->json(Estabelecimento::getResults($request->all()));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('configuracao');
        DB::beginTransaction();
        $formData = $request->all();
        $estabelecimento = Estabelecimento::with(['individuo.individuosEndereco'])->findOrFail($id);

        $estabelecimentoUpdate = $estabelecimento->update($formData);

        $individuoUpdate = true;
        if (isset($formData['individuo'])) {
            $individuoUpdate = Individuo::findOrFail($estabelecimento->individuo_id)->update($formData['individuo']);
        }

        $individuosEnderecoUpdate = true;
        if (isset($formData['individuo']['individuos_endereco'])) {
            $individuoUpdate = IndividuosEndereco::findOrFail($formData['individuo']['individuos_endereco']['id'])
                ->update($formData['individuo']['individuos_endereco']);
        }

        if ($estabelecimentoUpdate && $individuoUpdate && $individuosEnderecoUpdate) {
            DB::commit();
            return response()->json(['success' => true]);
        }

        DB::rollBack();
        return response()->json(['success' => false]);
    }

    public function buscarHorarioFuncionamento(Request $request)
    {
        authorizeAny(['configuracao', 'agenda']);
        return HorarioAtendimentoEstabelecimento::getResults($request->all());
    }

    public function horarioFuncionamento(Request $request)
    {
        authorizeAny(['configuracao']);
        $horarios = HorarioAtendimentoEstabelecimento::get();
        foreach ($horarios as $horario) {
            HorarioAtendimentoEstabelecimento::findOrFail($horario['id'])->delete();
        }

        DB::beginTransaction();
        $formData = $request->all();
        foreach ($formData as $dia) {
            foreach ($dia['horarios'] as $horario) {
                $horario = HorarioAtendimentoEstabelecimento::create(
                    array_merge($horario, ['dia' => $dia['dia']])
                );
            }
        }

        if ($horario) {
            DB::commit();
            return response()->json(['success' => true]);
        }

        DB::rollBack();
        return response()->json(['success' => false], 500);
    }

}
