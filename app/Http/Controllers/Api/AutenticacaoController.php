<?php

namespace App\Http\Controllers\Api;

use App\Events\CadastroSistemaEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CadastroSistemaRequest;
use App\Http\Requests\ConfirmacaoRequest;
use App\Managers\TenantManager;
use App\Models\ControleAcesso;
use App\Models\HorarioAtendimentoEstabelecimento;
use App\Models\Individuo;
use App\Models\IndividuosEndereco;
use App\Models\MeiosPagamento;
use App\Models\Profissional;
use App\Models\Servico;
use App\Models\Tenant;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AutenticacaoController extends Controller
{
    public function login(Request $request)
    {
        $usuario = Usuario::with(['individuo', 'estabelecimento', 'controleAcessos'])->where('email', $request->email)->first();
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $nome = $usuario->individuo->nome;
        $email = $usuario->email;
        $estabelecimento = $usuario->estabelecimento->uuid;
        $token = $usuario->createToken('vue_authenticate')->plainTextToken;
        TenantManager::set($usuario);

        $countHorarioAtendimentos = HorarioAtendimentoEstabelecimento::all()->count();
        $countMeiosPagamentos = MeiosPagamento::all()->count();
        $countServicos = Servico::all()->count();
        $profissionais = Profissional::with('servicos')->get();
        $countProfissionaisServicos = 0;

        foreach ($profissionais as $profissional) {
            if ($profissional->servicos->count()) {
                $countProfissionaisServicos++;
            }
        }

        return response()->json(compact('nome', 'token', 'email', 'estabelecimento', 'usuario',
            'countHorarioAtendimentos', 'countMeiosPagamentos', 'countServicos', 'countProfissionaisServicos'));
    }

    public function create(CadastroSistemaRequest $request)
    {
        $formData = $request->all();
        DB::beginTransaction();

        $objTenant = Tenant::create([
            'uuid' => Str::uuid(),
            'local_id' => $formData['local_id']
        ]);

        $individuoEsabelecimento = ['nome' => $formData['nome_empresa'], 'tipo_pessoa' => 'J'];
        $objIndividuoEstabelecimento = Individuo::create($individuoEsabelecimento);
        $objEstabelecimento = $objTenant->estabelecimentos()->create([
            'uuid' => Str::uuid(),
            'individuo_id' => $objIndividuoEstabelecimento->id,
            'tipos_estabelecimento_id' => $formData['tipos_estabelecimento_id'],
        ]);

        $individuoUsuario = ['nome' => $formData['nome'], 'tipo_pessoa' => 'F'];
        $objIndividuoUsuario = Individuo::create($individuoUsuario);
        $objUsuario = $objTenant->usuarios()->create([
            'uuid' => Str::uuid(),
            'email' => $formData['email'],
            'estabelecimento_id' => $objEstabelecimento->id,
            'individuo_id' => $objIndividuoUsuario->id
        ]);

        $objIndividuosEndereco = IndividuosEndereco::create(['individuo_id' => $objIndividuoEstabelecimento->id]);
        $servicoCategorias = [
            ['nome' => 'Barba'],
            ['nome' => 'Cabelo'],
            ['nome' => 'Depilação'],
            ['nome' => 'Sobrancelha'],
            ['nome' => 'Estética corporal'],
            ['nome' => 'Estética facial'],
            ['nome' => 'Manicure e pedicure'],
            ['nome' => 'Maquiagem'],
            ['nome' => 'Massagem'],
            ['nome' => 'Podologia'],
            ['nome' => 'Outros'],
        ];

        foreach ($servicoCategorias as $servicoCategoria) {
            $objTenant->servicoCategoria()->create(array_merge($servicoCategoria, [
                'estabelecimento_id' => $objEstabelecimento->id,
            ]));
        }

        $objProfissional = $objTenant->profissionais()->create([
            'estabelecimento_id' => $objEstabelecimento->id,
            'individuo_id' => $objIndividuoUsuario->id,
            'descontar_taxas_rateio' => 1
        ]);

        $controleAcessos = ControleAcesso::all()->pluck('id');
        $objUsuario->controleAcessos()->sync($controleAcessos);
        $objUsuario->update(['profissional_id' => $objProfissional->id]);

        if ($objTenant && $objEstabelecimento && $objIndividuoEstabelecimento
            && $objUsuario && $objIndividuosEndereco) {
            event(new CadastroSistemaEvent($objUsuario->id));
            DB::commit();
            return response()->json(['success' => true], 201);
        }

        return response()->json(['success' => false], 500);
    }

    public function confirmar(ConfirmacaoRequest $request)
    {
        $formData = $request->all();
        $tenant = Tenant::with(['estabelecimentos.individuo', 'usuarios.individuo'])
            ->where('uuid', $formData['uuid'])->first();

        Usuario::find($tenant->usuarios[0]->id)->update([
            'password' => bcrypt($formData['password'])
        ]);

        return response()->json('Usuário confirmado.');
    }

    public function buscarTenantUuid($uuid)
    {
        $tenant = Tenant::with(['estabelecimentos.individuo', 'usuarios.individuo'])
            ->where('uuid', $uuid)->first();

        $usuario = DB::table('usuarios')->find($tenant->usuarios[0]->id);
        if ($usuario && is_null($usuario->password)) {
            $nome = $tenant->usuarios[0]->individuo->nome;
            $email = $tenant->usuarios[0]->email;
            $nomeEmpresa = $tenant->estabelecimentos[0]->individuo->nome;

            return response()->json(compact('nome', 'email', 'nomeEmpresa'));
        }

        return response()->json('Usuário já cadastrado', 403);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['Deslogado com sucesso']);
    }

    public function usuarios(Request $request)
    {
        return response()->json(Usuario::getResults($request->all()));
    }

    public function meusDados(Request $request)
    {
        $formData = $request->all();
        $usuario = Usuario::with(['individuo'])->find(TenantManager::get()->id);

        if (isset($formData['alterarSenha']) && $formData['alterarSenha']) {
            $usuario->update(['password' => bcrypt($formData['password'])]);
        }

        $usuario->individuo()->update(['nome' => $formData['nome'], 'celular' => so_numero($formData['celular'])]);
    }

    public function controleAcesso()
    {
        $acessos = ControleAcesso::all();
        return response()->json($acessos);
    }
}
