<?php

use Illuminate\Database\Seeder;

class TiposMovimentacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['nome' => '(+) Compra/Bonificação', 'entrada' => true, 'sigla' => 'E-CB'],
            ['nome' => '(+) Devolução de venda', 'entrada' => true, 'sigla' => 'E-D'],
            ['nome' => '(-) Devolução de compra', 'entrada' => false, 'sigla' => 'S-DC'],
            ['nome' => '(-) Consumo interno', 'entrada' => false, 'sigla' => 'S-CI'],
            ['nome' => '(-) Perda/Quebra/Deterioração', 'entrada' => false, 'sigla' => 'S-PQD'],
            ['nome' => '(-) Venda', 'entrada' => false, 'sigla' => 'S-V'],
        ];

        foreach ($data as $item) {
            \App\Models\TipoMovimentacoesEstoque::create($item);
        }
    }
}
