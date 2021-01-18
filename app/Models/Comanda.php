<?php

namespace App\Models;

use App\Models\Traits\EstabelecimentoTrait;
use App\Models\Traits\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comanda extends Model
{
    use SoftDeletes, Search, EstabelecimentoTrait;

    protected $fillable = [
        'caixa_id', 'cliente_id', 'tipo', 'tipo_desconto_geral', 'valor_desconto_total', 'valor_total', 'valor_pago',
        'valor_pendente', 'data', 'status', 'estornada'
    ];

    public function comandaItens()
    {
        return $this->hasMany(ComandaItem::class);
    }

    public function comandaFormaPagamentos()
    {
        return $this->hasMany(ComandaFormaPagamento::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public static function movimentarEstoque($comanda, $siglaMovimentacao = 'S-V')
    {
        if ($comanda['status'] === 'F' && isset($comanda['comanda_itens'])) {
            $tipoMovimentacao = TipoMovimentacoesEstoque::where('sigla', $siglaMovimentacao)->first();
            foreach ($comanda['comanda_itens'] as $comanda_item) {
                if ($comanda_item['produto_id']) {
                    $movimentacaoEstoque = MovimentacoesEstoque::create([
                        'tipo_movimentacoes_estoque_id' => $tipoMovimentacao->id,
                        'cliente_id' => $comanda['cliente_id']
                    ]);

                    $produto = Produto::findOrFail($comanda_item['produto_id']);
                    $custoTotal = $produto->valor_fracionado * $comanda_item['quantidade'];

                    $quantidadeTotal = $tipoMovimentacao->entrada ? $produto->quantidade + $comanda_item['quantidade'] :
                        $produto->quantidade - $comanda_item['quantidade'];

                    $saldoCustoTotal = $tipoMovimentacao->entrada ? $produto->custo_total + $custoTotal :
                        $produto->custo_total - $custoTotal;

                    $movimentacaoEstoque->produtosMovimentacoesEstoques()->attach($comanda_item['produto_id'], [
                        'quantidade' => $comanda_item['quantidade'],
                        'unidade_medida' => $comanda_item['unidade_medida'],
                        'valor_unitario' => $produto->valor_fracionado,
                        'quantidade_total' => $quantidadeTotal,
                        'custo_total' => $custoTotal,
                        'saldo_custo_total' => $saldoCustoTotal,
                    ]);

                    MovimentacoesEstoque::addMovimentacaoProdutoEstoque($comanda_item['produto_id'],
                        $tipoMovimentacao->id, $comanda_item['quantidade'], $custoTotal);
                }
            }
        }
    }
}
