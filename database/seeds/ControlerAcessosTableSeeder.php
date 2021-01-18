<?php

use Illuminate\Database\Seeder;

class ControlerAcessosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['nome' => 'Administrativo', 'chave' => 'administrativo'],
            ['nome' => 'Agenda', 'chave' => 'agenda'],
            ['nome' => 'Caixa', 'chave' => 'caixa'],
            ['nome' => 'Clientes', 'chave' => 'clientes'],
            ['nome' => 'Profissionais', 'chave' => 'profissionais'],
            ['nome' => 'Serviços', 'chave' => 'servicos'],
            ['nome' => 'Estoque', 'chave' => 'estoque'],
            ['nome' => 'Fornecedores', 'chave' => 'fornecedores'],
            ['nome' => 'Relatórios', 'chave' => 'relatorios'],
            ['nome' => 'Configuração', 'chave' => 'configuracao'],
        ];

        foreach ($data as $item) {
            \App\Models\ControleAcesso::create($item);
        }
    }
}
