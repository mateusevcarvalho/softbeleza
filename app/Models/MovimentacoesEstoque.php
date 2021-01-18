<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;

class MovimentacoesEstoque extends Model
{
    use Search, EstabelecimentoTrait;

    protected $fillable = [
        'estabelecimento_id', 'tipo_movimentacoes_estoque_id', 'fornecedor_id', 'nota_fiscal', 'observacoes',
        'profissional_id', 'cliente_id'
    ];

    public function produtosMovimentacoesEstoques()
    {
        return $this->belongsToMany(Produto::class, 'produtos_movimentacoes_estoques',
            'movimentacoes_estoque_id', 'produto_id')
            ->withPivot('quantidade', 'unidade_medida', 'valor_unitario', 'quantidade_total', 'custo_total',
                'saldo_custo_total');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class)->withTrashed();
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'profissional_id')->withTrashed();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }


    public function tipoMovimentacoesEstoque()
    {
        return $this->belongsTo(TipoMovimentacoesEstoque::class);
    }

    public static function addMovimentacaoProdutoEstoque($produtoId, $tipoMovimentacaoId, $quantidadeTotal, $custoTotal)
    {
        $produto = Produto::findOrfail($produtoId);
        $tipoMovimentacao = TipoMovimentacoesEstoque::find($tipoMovimentacaoId);

        if ($tipoMovimentacao->entrada) {
            $quantidadeTotal = $produto->quantidade + $quantidadeTotal;
            $custoTotal = $produto->custo_total + $custoTotal;
        } else {
            $quantidadeTotal = $produto->quantidade - $quantidadeTotal;
            $custoTotal = $produto->custo_total - $custoTotal;
        }

        $produto->update(['quantidade' => $quantidadeTotal, 'custo_total' => $custoTotal]);
    }
}
