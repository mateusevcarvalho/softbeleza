<?php

use App\Models\Estado;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

class EstadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://servicodados.ibge.gov.br/api/v1/localidades/estados');
        $estados = json_decode($response->getBody(), true);

        foreach ($estados as $estado) {
            Estado::create([
                'nome' => $estado['nome'],
                'sigla' => $estado['sigla'],
                'ibge' => $estado['id']
            ]);
        }
    }
}
