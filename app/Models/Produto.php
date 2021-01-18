<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use EstabelecimentoTrait, Search, SoftDeletes;

    protected $fillable = [
        'estabelecimento_id', 'nome', 'valor', 'desconto_maximo', 'desconto_promocional', 'descricao', 'quantidade',
        'unidade_medida_entrada', 'unidade_medida_saida', 'valor_unitario', 'valor_fracionado', 'equivalencia',
        'custo_total'
    ];

    public function movimentacoesEstoques()
    {
        return $this->belongsToMany(MovimentacoesEstoque::class,
            'produtos_movimentacoes_estoques', 'produto_id', 'movimentacoes_estoque_id')
            ->withPivot('quantidade', 'unidade_medida', 'valor_unitario', 'quantidade_total', 'custo_total',
                'saldo_custo_total')->orderBy('created_at', 'DESC');
    }

}
