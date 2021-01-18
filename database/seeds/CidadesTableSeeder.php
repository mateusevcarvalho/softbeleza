<?php

use App\Models\Cidade;
use App\Models\Estado;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

class CidadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        $estados = Estado::all();

        foreach ($estados as $estado) {
            $response = $client->request('GET',
                "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$estado->ibge}/municipios");

            $cidades = json_decode($response->getBody(), true);
            foreach ($cidades as $cidade) {
                Cidade::create([
                    'estado_id' => $estado->id,
                    'nome' => $cidade['nome'],
                    'ibge' => $cidade['id']
                ]);
            }
        }
    }
}
