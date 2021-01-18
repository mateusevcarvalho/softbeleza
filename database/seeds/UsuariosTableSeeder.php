<?php

use App\Models\ControleAcesso;
use App\Models\Individuo;
use App\Models\IndividuosEndereco;
use App\Models\ServicoCategoria;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsuariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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

        $formData = [
            'local_id' => 2,
            'nome_empresa' => 'Salão do Mateus',
            'tipos_estabelecimento_id' => 2,
            'nome' => 'Mateus Carvalho',
            'email' => 'mateus@email.com'
        ];

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
            'individuo_id' => $objIndividuoUsuario->id,
            'password' => bcrypt('123456')
        ]);

        IndividuosEndereco::create(['individuo_id' => $objIndividuoEstabelecimento->id]);
        foreach ($servicoCategorias as $servicoCategoria) {
            ServicoCategoria::create(array_merge($servicoCategoria, [
                'tenant_id' => $objTenant->id,
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
    }
}
