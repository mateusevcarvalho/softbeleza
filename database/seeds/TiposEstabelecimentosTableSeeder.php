<?php

use App\Models\TiposEstabelecimento;
use Illuminate\Database\Seeder;

class TiposEstabelecimentosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nome' => 'Salão de Beleza'],
            ['nome' => 'Barbearia'],
            ['nome' => 'Clínica Estética'],
            ['nome' => 'Esmalteria'],
            ['nome' => 'Centro de depilação'],
            ['nome' => 'SPA'],
            ['nome' => 'Maquiadora'],
            ['nome' => 'Espaço Beleza'],
            ['nome' => 'Podologia'],
            ['nome' => 'Outros'],
        ];

        foreach ($tipos as $tipo) {
            TiposEstabelecimento::create($tipo);
        }
    }
}
