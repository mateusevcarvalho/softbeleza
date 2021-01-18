<template>
    <page-component title="Lançar Movimentação" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <form @submit.prevent="onSubmit">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Tipo Movimentação <b class="text-danger">*</b></label>
                        <select class="form-control" v-model="form.tipo_movimentacoes_estoque_id"
                                @change="onChangeTipo" :disabled="!!form.produtos_movimentacoes_estoques.length"
                                required>
                            <option v-for="tipo in tiposMovimentacoes" :value="tipo.id">
                                {{ tipo.nome }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group col-sm-4"
                         v-if="tipoMovimentacaoSigla === 'E-CB' || tipoMovimentacaoSigla === 'S-DC'">
                        <label>Nota Fiscal</label>
                        <input type="text" class="form-control" v-model="form.nota_fiscal">
                    </div>

                    <div class="col-sm-4 form-group"
                         v-if="tipoMovimentacaoSigla === 'E-CB' || tipoMovimentacaoSigla === 'S-DC'">
                        <label>Fornecedor</label>
                        <v-select :options="fornecedores"
                                  placeholder="-Selecione-"
                                  :reduce="fornecedor => fornecedor.id"
                                  :getOptionLabel="fornecedor => fornecedor.individuo.nome"
                                  v-model="form.fornecedor_id">
                            <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                        </v-select>
                    </div>

                    <div class="col-sm-4 form-group"
                         v-if="tipoMovimentacaoSigla === 'E-D' || tipoMovimentacaoSigla === 'S-V'">
                        <label>Cliente</label>
                        <v-select :options="clientes"
                                  placeholder="-Selecione-"
                                  @search="onSearch"
                                  :reduce="cliente => cliente.id"
                                  :getOptionLabel="cliente => cliente.individuo.nome"
                                  v-model="form.cliente_id">
                            <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                        </v-select>
                    </div>

                    <div class="col-sm-4 form-group"
                         v-if="tipoMovimentacaoSigla === 'S-CI'">
                        <label>Profissionais</label>
                        <v-select :options="profissionais"
                                  placeholder="-Selecione-"
                                  :reduce="profissional => profissional.id"
                                  :getOptionLabel="profissional => profissional.individuo.nome"
                                  v-model="form.profissional_id">
                            <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                        </v-select>
                    </div>

                    <div class="col-sm-12 form-group">
                        <label>Observações</label>
                        <textarea class="form-control" v-model="form.observacoes"></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary"
                                @click.prevent="$bvModal.show('modal-add-produtos')">
                            <i class="fas fa-plus"></i> Adicionar Produto
                        </button>
                    </div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Valor Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(movimentacao, index) in form.produtos_movimentacoes_estoques">
                        <td>{{ movimentacao.produto_nome }}</td>
                        <td>{{ movimentacao.quantidade }} {{ movimentacao.unidade_medida }}</td>
                        <td>{{ movimentacao.custo_total | money }}</td>
                        <td width="50">
                            <span class="text-danger" style="cursor: pointer" @click="removeProduto(index)">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot v-if="!form.produtos_movimentacoes_estoques.length">
                    <tr class="text-center">
                        <td colspan="4" class="text-center table-light">Nenhum produto adicionado.</td>
                    </tr>
                    </tfoot>
                    <tfoot v-if="form.produtos_movimentacoes_estoques.length">
                    <tr>
                        <td colspan="2" class="table-light">Total:</td>
                        <td colspan="2">{{ totalCustos | money }}</td>
                    </tr>
                    </tfoot>
                </table>

                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <router-link :to="{name: 'estoque'}" class="btn btn-dark">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </router-link>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="submit" class="btn btn-primary" :disabled="load">
                            Salvar
                            <span v-if="load">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                            <span v-else><i class="fas fa-save"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </card-component>

        <b-modal size="xl" id="modal-add-produtos" title="Adicionar produto" hide-footer>
            <form-add-produtos
                :tipo-movimentacao="tipoMovimentacao"
                :tipo-movimentacao-sigla="tipoMovimentacaoSigla"
                :produtos="produtos"
                @adicionarProduto="handleAdicionarProduto" />
        </b-modal>
    </page-component>
</template>

<script>
import {mapActions} from 'vuex'
import axios from "axios";
import lodash from 'lodash'
import FormAddProdutos from "./FormAddProdutos";

export default {
    name: "LancarMovimentacao",
    components: {FormAddProdutos},
    created() {
        this.load = true;
        axios.all([
            this.buscarTiposMovimentacoesEstoque(),
            this.buscarFornecedores('with[]=individuo&where[status]=A'),
            this.buscarProfissionais('with[]=individuo&where[status]=A'),
            this.buscarProdutos(),
        ]).then(axios.spread((resTiposMovimentacoes, resFornecedores, resProf, resProdutos) => {
            this.tiposMovimentacoes = resTiposMovimentacoes.data;
            this.fornecedores = resFornecedores.data;
            this.profissionais = resProf.data;
            this.produtos = resProdutos.data;
            this.tipoMovimentacao = this.tiposMovimentacoes.find(tipo => tipo.id === this.form.tipo_movimentacoes_estoque_id)
            this.load = false;
        })).catch(error => {
            this.$store.dispatch('handleCatchError', error);
            this.load = false;
        });
    },
    data() {
        return {
            load: false,
            tiposMovimentacoes: [],
            fornecedores: [],
            clientes: [],
            produtos: [],
            profissionais: [],
            tipoMovimentacao: {
                entrada: false,
                sigla: ''
            },
            form: {
                tipo_movimentacoes_estoque_id: 1,
                fornecedor_id: '',
                cliente_id: '',
                profissional_id: '',
                observacoes: '',
                nota_fiscal: '',
                produtos_movimentacoes_estoques: [],
            },
            breadcrumb: [
                {name: 'Estoque', to: 'estoque'},
                {name: 'Lançar Movimentação', to: null}
            ],
        }
    },
    computed: {
        tipoMovimentacaoSigla() {
            if (this.tiposMovimentacoes.length) {
                const tipo = this.tiposMovimentacoes.find(tipo => tipo.id === this.form.tipo_movimentacoes_estoque_id);
                return tipo.sigla;
            }

            return ''
        },

        totalCustos() {
            const arrSum = arr => arr.reduce((a, b) => a + b.custo_total, 0);
            return arrSum(this.form.produtos_movimentacoes_estoques);
        }
    },
    methods: {
        ...mapActions(['buscarTiposMovimentacoesEstoque', 'buscarFornecedores', 'buscarClientes',
            "buscarProfissionais", "buscarProdutos"]),

        onSubmit() {
            if (this.form.produtos_movimentacoes_estoques.length) {
                this.load = true;
                axios.post('/api/movimentacoes-estoque', this.form).then(_ => {
                    this.$snotify.success('Movimentação lançada.', 'Sucesso');
                    this.form = {
                        tipo_movimentacoes_estoque_id: 1,
                        fornecedor_id: '',
                        cliente_id: '',
                        profissional_id: '',
                        observacoes: '',
                        nota_fiscal: '',
                        produtos_movimentacoes_estoques: [],
                    };
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            } else {
                this.$snotify.warning('Nenhum produto adicionado.', 'Alerta')
            }
        },

        removeProduto(index) {
            this.form.produtos_movimentacoes_estoques.splice(index, 1);
        },

        handleAdicionarProduto(movimentacao) {
            this.form.produtos_movimentacoes_estoques.push({...movimentacao})
        },

        onChangeTipo() {
            this.form.cliente_id = '';
            this.form.fornecedor_id = '';
            this.form.profissional_id = '';
            this.form.observacoes = '';
            this.tipoMovimentacao = this.tiposMovimentacoes.find(tipo => tipo.id === this.form.tipo_movimentacoes_estoque_id)
        },

        onSearch(search, loading) {
            if (search) {
                loading(true);
                this.search(loading, search, this);
            }
        },

        search: lodash.debounce((loading, search, vm) => {
            const path = `&whereLikeHas[individuo]=nome,${search}`;
            vm.buscarClientes('with[]=individuo' + path).then(response => {
                vm.clientes = response.data;
                loading(false)
            });
        }, 350),
    }
}
</script>

<style scoped>

</style>
