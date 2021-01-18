<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovimentacoesEstoque;
use App\Models\Produto;
use App\Models\TipoMovimentacoesEstoque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimentacoesEstoquesController extends Controller
{
    public function index(Request $request)
    {
        authorizeAny(['estoque', 'relatorios']);
        return response()->json(MovimentacoesEstoque::getResults($request->all()));
    }

    public function store(Request $request)
    {
        authorizeAny(['estoque']);
        $formData = $request->all();
        $movimentacaoEstoque = MovimentacoesEstoque::create($formData);
        $tipoMovimentacao = TipoMovimentacoesEstoque::findOrFail($formData['tipo_movimentacoes_estoque_id']);
        foreach ($formData['produtos_movimentacoes_estoques'] as $movimentacao) {
            $produto = Produto::findOrFail($movimentacao['produto_id']);
            $saldoCustoTotal = $tipoMovimentacao->entrada ? ($produto->custo_total + $movimentacao['custo_total']) :
                ($produto->custo_total - $movimentacao['custo_total']);

            $quantidadeEstoque = $produto->unidade_medida_saida == $movimentacao['unidade_medida'] ?
                $movimentacao['quantidade'] : $movimentacao['quantidade'] * $produto->equivalencia;

            $quantidadeTotal = $tipoMovimentacao->entrada ? $quantidadeEstoque + $produto->quantidade :
                $produto->quantidade - $quantidadeEstoque;

            $movimentacaoEstoque->produtosMovimentacoesEstoques()->attach($movimentacao['produto_id'], [
                'quantidade' => $movimentacao['quantidade'],
                'unidade_medida' => $movimentacao['unidade_medida'],
                'valor_unitario' => $movimentacao['valor_unitario'],
                'quantidade_total' => $quantidadeTotal,
                'custo_total' => $movimentacao['custo_total'],
                'saldo_custo_total' => $saldoCustoTotal,
            ]);

            MovimentacoesEstoque::addMovimentacaoProdutoEstoque($movimentacao['produto_id'],
                $formData['tipo_movimentacoes_estoque_id'], $quantidadeEstoque, $movimentacao['custo_total']);
        }

        return response()->json(['success' => true]);
    }

    public function tiposMovimentacoes()
    {
        authorizeAny(['estoque']);
        return response()->json(TipoMovimentacoesEstoque::all());
    }
}
