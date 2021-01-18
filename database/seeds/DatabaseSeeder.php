<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LocaisTableSeeder::class);
        $this->call(TiposEstabelecimentosTableSeeder::class);
        $this->call(EstadosTableSeeder::class);
        $this->call(CidadesTableSeeder::class);
        $this->call(TiposMovimentacoesTableSeeder::class);
        $this->call(ControlerAcessosTableSeeder::class);
        //$this->call(UsuariosTableSeeder::class);
    }
}
