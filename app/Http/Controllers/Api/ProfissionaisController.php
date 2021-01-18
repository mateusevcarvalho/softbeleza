<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HorarioAtendimentoProfissional;
use App\Models\Individuo;
use App\Models\Profissional;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfissionaisController extends Controller
{

    public function index(Request $request)
    {
        authorizeAny(['profissionais', 'agenda', 'caixa', 'estoque', 'relatorios']);
        return response()->json(Profissional::getResults($request->all()));
    }

    public function store(Request $request)
    {
        authorizeAny(['profissionais']);
        DB::beginTransaction();
        $formData = $request->all();
        $objIndividuo = Individuo::create($formData['individuo']);
        $objProfissional = Profissional::create(array_merge($formData, ['individuo_id' => $objIndividuo->id]));

        if (isset($formData['usuario']['email']) && $formData['usuario']['email']) {
            $objUsuario = Usuario::create([
                'individuo_id' => $objIndividuo->id,
                'profissional_id' => $objProfissional->id,
                'uuid' => Str::uuid(),
                'email' => $formData['usuario']['email'],
                'password' => bcrypt($formData['usuario']['password']),
                'status' => $formData['status'] ? 'A' : 'I',
            ]);
            $objUsuario->controleAcessos()->sync($formData['usuario']['acessos']);
        }

        if ($objIndividuo && $objProfissional) {
            DB::commit();
            return response()->json(['success' => true]);
        }

        DB::rollBack();
        return response()->json(['success' => false], 500);
    }

    public function show($id)
    {
        authorizeAny(['profissionais']);
        $profissional = Profissional::with('individuo')->findOrFail($id);
        return response()->json($profissional);
    }

    public function update(Request $request, $id)
    {
        authorizeAny(['profissionais']);
        DB::beginTransaction();
        $formData = $request->all();
        $profissional = Profissional::with('individuo')->findOrFail($id);
        $profissionalUpdate = $profissional->update($formData);
        $individuoUpdate = Individuo::find($profissional->individuo_id)->update($formData['individuo']);

        if (isset($formData['usuario']['id'])
            && isset($formData['usuario']['email'])
            && $formData['usuario']['email']) {
            $objUsuario = Usuario::find($formData['usuario']['id']);
            $objUsuario->update(['email' => $formData['usuario']['email'], 'status' => $formData['status'] ? 'A' : 'I']);
            $objUsuario->controleAcessos()->sync($formData['usuario']['acessos']);
        }

        if (!isset($formData['usuario']['id'])
            && isset($formData['usuario']['email'])
            && $formData['usuario']['email']
            && $formData['usuario']['password']) {
            $objUsuario = Usuario::create([
                'individuo_id' => $profissional->individuo_id,
                'profissional_id' => $profissional->id,
                'uuid' => Str::uuid(),
                'email' => $formData['usuario']['email'],
                'password' => bcrypt($formData['usuario']['password']),
                'status' => $formData['status'] ? 'A' : 'I',
            ]);

            $objUsuario->controleAcessos()->sync($formData['usuario']['acessos']);
        }


        if ($profissionalUpdate && $individuoUpdate) {
            DB::commit();
            return response()->json(['success' => true]);
        }

        DB::rollBack();
        return response()->json(['success' => false], 500);
    }

    public function destroy($id)
    {
        authorizeAny(['profissionais']);
        $profissional = Profissional::findOrFail($id);
        if ($profissional->delete()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }

    public function storeServicos(Request $request)
    {
        authorizeAny(['profissionais']);
        $formData = $request->all();
        $profissional = Profissional::with('servicos')->find($formData['profissional_id']);
        $profissional->servicos()->attach($formData['servico_id'], [
            'rateio' => $formData['rateio'],
            'duracao' => $formData['duracao'],
            'valor' => $formData['valor']
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyServicos(Request $request, $id)
    {
        authorizeAny(['profissionais']);
        $profissional = Profissional::with('servicos')->find($id);
        $profissional->servicos()->detach($request->get('servico_id'));

        return response()->json(['success' => true]);
    }

    public function showHorarios(Request $request)
    {
        authorizeAny(['profissionais']);
        return response()->json(HorarioAtendimentoProfissional::getResults($request->all()));
    }

    public function horarios(Request $request)
    {
        authorizeAny(['profissionais']);
        $formData = $request->all();
        $horarios = HorarioAtendimentoProfissional::where('profissional_id', $formData['profissional_id'])->get();
        foreach ($horarios as $horario) {
            HorarioAtendimentoProfissional::findOrFail($horario['id'])->delete();
        }

        DB::beginTransaction();
        foreach ($formData['dias'] as $dia) {
            foreach ($dia['horarios'] as $horario) {
                $horario = HorarioAtendimentoProfissional::create(
                    array_merge($horario, ['dia' => $dia['dia'], 'profissional_id' => $formData['profissional_id']])
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
