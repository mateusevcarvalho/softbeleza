<template>
    <page-component :title="'Extrato de ' + produto.nome" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <b-table
                    hover
                    small
                    bordered
                    responsive
                    :fields="table.fields"
                    :current-page="table.currentPage"
                    :per-page="table.perPage"
                    :items="montarTabela(movimentacoes)">
                <template v-slot:thead-top="data">
                    <b-tr>
                        <b-th colspan="3"></b-th>
                        <b-th colspan="2" class="text-center">Movimentação</b-th>
                        <b-th colspan="2" class="text-center">Saldo</b-th>
                    </b-tr>
                </template>
            </b-table>

            <b-pagination
                    v-if="movimentacoes.length > table.perPage"
                    v-model="table.currentPage"
                    :total-rows="movimentacoes.length"
                    :per-page="table.perPage"
                    class="mt-3 text-center"/>

            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <router-link :to="{name: 'estoque'}" class="btn btn-dark">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </router-link>
                </div>
            </div>
        </card-component>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'

    export default {
        name: "ExtratoProduto",
        created() {
            this.load = true;
            let path = 'with[]=movimentacoesEstoques.tipoMovimentacoesEstoque';
            path += '&with[]=movimentacoesEstoques.cliente.individuo';
            path += '&with[]=movimentacoesEstoques.profissional.individuo';
            path += '&with[]=movimentacoesEstoques.fornecedor.individuo';
            path += `&where[id]=${this.$route.params.id}&first=true`;

            this.buscarProdutos(path).then(response => {
                this.movimentacoes = response.data.movimentacoes_estoques;
                this.produto = response.data;
                this.load = false;
            }).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },
        data() {
            return {
                load: false,
                movimentacoes: [],
                table: {
                    currentPage: 1,
                    perPage: 15,
                    fields: [
                        {key: 'data', label: 'Data'},
                        {key: 'operacao', label: 'Operação'},
                        {key: 'cliente_fornecedor', label: 'Cliente/Fornecedor'},
                        {key: 'mov_qtd', label: 'Qtd.'},
                        {key: 'mov_custo_total', label: 'Custo Total'},
                        {key: 'saldo_qtd', label: 'Qtd.'},
                        {key: 'saldo_custo_total', label: 'Custo Total'},
                    ],
                },
                produto: {
                    nome: '',
                    valor: '',
                    desconto_maximo: 0,
                    desconto_promocional: 0,
                    descricao: '',
                    unidade_medida_entrada: '',
                    unidade_medida_saida: '',
                    valor_unitario: '',
                    valor_fracionado: '',
                    equivalencia: 0,
                },
                breadcrumb: [
                    {name: 'Estoque', to: 'estoque'},
                    {name: 'Extrato do produto', to: null}
                ],
            }
        },
        methods: {
            ...mapActions(["buscarProdutos"]),

            montarTabela(data) {
                const newData = [];
                data.forEach(item => {
                    newData.push({
                        data: this.$options.filters.dateTimeBr(item.created_at),
                        operacao: item.tipo_movimentacoes_estoque.nome,
                        cliente_fornecedor: this.mostrarIndividuo(item),
                        mov_qtd: item.pivot.quantidade + ' ' + item.pivot.unidade_medida,
                        mov_custo_total: this.$options.filters.money(item.pivot.custo_total),
                        saldo_qtd: this.formatarQuantidade(item.pivot.quantidade_total, this.produto),
                        saldo_custo_total: this.$options.filters.money(item.pivot.saldo_custo_total),

                    });
                });

                return newData;
            },

            mostrarIndividuo(movimentacao) {
                if (movimentacao.cliente_id) {
                    return movimentacao.cliente.individuo.nome;
                } else if (movimentacao.fornecedor_id) {
                    return movimentacao.fornecedor.individuo.nome;
                } else if (movimentacao.proficional_id) {
                    return movimentacao.proficional.individuo.nome;
                }

                return '';
            },

            formatarQuantidade(quantidade, produto) {
                if (produto) {
                    if (produto.unidade_medida_entrada === produto.unidade_medida_saida) {
                        return `${quantidade} ${produto.unidade_medida_saida}`;
                    } else {
                        const unidadeEntrada = parseInt(quantidade) / parseInt(produto.equivalencia);
                        if (unidadeEntrada < 1) {
                            return `${quantidade} ${produto.unidade_medida_saida}`;
                        } else {
                            const mod = parseInt(quantidade) % parseInt(produto.equivalencia);
                            if (mod) {
                                return `${parseInt(unidadeEntrada)} ${produto.unidade_medida_entrada}, ${(mod)} ${produto.unidade_medida_saida}`;
                            } else {
                                return `${parseInt(unidadeEntrada)} ${produto.unidade_medida_entrada}`;
                            }
                        }
                    }
                }

                return 0;
            },
        }
    }
</script>

<style scoped>

</style>
