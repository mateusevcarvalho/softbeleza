<?php

use App\Models\Local;
use Illuminate\Database\Seeder;

class LocaisTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locais = [
            ['nome' => 'Indicações'],
            ['nome' => 'Vendedor'],
            ['nome' => 'Rede Sociais'],
            ['nome' => 'Sites de Pesquisa (Google, Bing...)'],
        ];

        foreach ($locais as $local) {
            Local::create($local);
        }
    }
}
