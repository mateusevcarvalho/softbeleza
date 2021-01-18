<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col-sm-4 form-group">
                <label>Produto </label>
                <v-select :options="produtos"
                          placeholder="-Selecione-"
                          @input="onChangeProdutos"
                          :reduce="produto => produto.id"
                          :getOptionLabel="produto => produto.nome"
                          v-model="form.produto_id">
                    <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                </v-select>
            </div>

            <div class="col-sm-2 form-group">
                <label>Quantidade </label>
                <input type="number" class="form-control" v-model="form.quantidade" @change="onChangeQuantidade">
            </div>

            <div class="form-group col-sm-3" v-if="form.produto_id">
                <label>Un. Medida</label>
                <select v-model="form.unidade_medida" class="form-control" @change="onChangeUnidades" required>
                    <option value="">-Selecione-</option>
                    <option :value="unidade.value" v-for="unidade in unidadesMedidas">{{ unidade.label }}</option>
                </select>
            </div>

            <div class="col-sm-3 form-group">
                <label>Custo Un. Compra</label>
                <money class="form-control" v-model="form.valor_unitario"></money>
            </div>

            <div class="col-sm-4 form-group">
                <label>Estoque Atual</label>
                <input type="text" class="form-control" :value="formatarQuantidade(estoqueAtual)" disabled>
            </div>

            <div class="col-sm-4 form-group">
                <label>Valor Total</label>
                <money class="form-control" v-model="form.custo_total" disabled></money>
            </div>

            <div class="col-sm-4 form-group">
                <label>Estoque Após Movimentação</label>
                <input type="text" class="form-control" :value="formatarQuantidade(estoqueTotal)" disabled>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-add-produtos')">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" :disabled="load">
                    Adicionar
                    <span v-if="load">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </span>
                    <span v-else><i class="fas fa-plus"></i></span>
                </button>
            </div>
        </div>
    </form>
</template>

<script>
import lodash from "lodash";
import {mapActions} from 'vuex';

export default {
    name: "FormAddProdutos",
    props: {
        produtos: {
            required: true
        },
        tipoMovimentacaoSigla: {
            required: true
        },
        tipoMovimentacao: {
            required: true
        }
    },
    data() {
        return {
            load: false,
            form: {
                produto_id: '',
                produto_nome: '',
                quantidade: 0,
                unidade_medida: '',
                valor_unitario: 0,
                quantidade_total: '',
                custo_total: 0
            }
        }
    },
    computed: {
        unidadesMedidas() {
            const unidades = [];
            const produto = this.produtos.find(produto => produto.id === this.form.produto_id);

            if (produto) {
                if (produto.unidade_medida_entrada === produto.unidade_medida_saida) {
                    unidades.push({value: produto.unidade_medida_entrada, label: produto.unidade_medida_entrada});
                } else {
                    unidades.push({value: produto.unidade_medida_entrada, label: produto.unidade_medida_entrada});
                    unidades.push({value: produto.unidade_medida_saida, label: produto.unidade_medida_saida});
                }
            }

            return unidades;
        },

        estoqueAtual() {
            const produto = this.produtos.find(produto => produto.id === this.form.produto_id);
            return produto ? produto.quantidade : 0;
        },

        estoqueTotal() {
            const produto = this.produtos.find(produto => produto.id === this.form.produto_id);
            let total = 0;
            if (produto) {
                if (produto.unidade_medida_saida === this.form.unidade_medida) {
                    if (this.tipoMovimentacao.entrada) {
                        total = parseInt(this.estoqueAtual) + parseInt(this.form.quantidade);
                    } else {
                        total = parseInt(this.estoqueAtual) - parseInt(this.form.quantidade);
                    }
                } else {
                    if (this.tipoMovimentacao.entrada) {
                        total = parseInt(this.estoqueAtual) + (parseInt(this.form.quantidade) * produto.equivalencia);
                    } else {
                        total = parseInt(this.estoqueAtual) - (parseInt(this.form.quantidade) * produto.equivalencia);
                    }
                }
            }

            return total;
        }
    },
    methods: {
        ...mapActions(["buscarProdutos"]),

        formatarQuantidade(quantidade) {
            const produto = this.produtos.find(produto => produto.id === this.form.produto_id);
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

        onChangeQuantidade() {
            this.form.custo_total = this.form.valor_unitario * this.form.quantidade;
        },

        onSubmit() {
            if (this.form.produto_id) {
                const produto = this.produtos.find(produto => produto.id === this.form.produto_id);
                this.form.quantidade_total = this.estoqueTotal;
                this.form.produto_nome = produto.nome;
                this.$emit('adicionarProduto', {...this.form});
                this.form = {
                    produto_id: '',
                    quantidade: 0,
                    unidade_medida: '',
                    valor_unitario: 0,
                    quantidade_total: '',
                    custo_total: 0
                };
                this.produtos = [];
                this.$bvModal.hide('modal-add-produtos');
            } else {
                this.$snotify.warning('Produto é obrigatório', 'Alerta');
            }
        },

        onChangeUnidades() {
            const produto = this.produtos.find(produto => produto.id === this.form.produto_id);
            if (this.form.unidade_medida === produto.unidade_medida_entrada) {
                this.form.valor_unitario = produto.valor_unitario;
            } else {
                this.form.valor_unitario = produto.valor_fracionado;
            }

            this.onChangeQuantidade();
        },

        onChangeProdutos() {
            const produto = this.produtos.find(produto => produto.id === this.form.produto_id);
            if (produto) {
                this.form.unidade_medida = produto.unidade_medida_entrada;
                this.form.valor_unitario = produto.valor_unitario;
            }
        },
    }
}
</script>

<style scoped>

</style>
