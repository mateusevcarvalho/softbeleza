<template>
    <page-component title="Estoque" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="row mb-3">
                <div class="col-sm-8">
                    <button type="button" class="btn btn-primary" @click.prevent="cadastrar">
                        <i class="fas fa-plus"></i> Cadastrar
                    </button>
                    <router-link :to="{name: 'lancarMovimentacao'}" type="button" class="btn btn-dark">
                        <i class="fas fa-file-invoice-dollar"></i> Lançar Movimentação
                    </router-link>
                </div>
                <div class="col-sm-4">
                    <b-input-group>
                        <b-form-input placeholder="Procure pelo nome" @keypress.enter="handleLoadData()"
                                      v-model="search"></b-form-input>
                        <b-input-group-append>
                            <b-button variant="primary" @click="handleLoadData()">
                                <i class="fas fa-search"></i> Buscar
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Valor de Venda</th>
                        <th>Quantidade</th>
                        <th>Custo Total</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="itemProduto in produtos.data">
                        <td>{{itemProduto.nome}}</td>
                        <td>{{itemProduto.valor | money}}</td>
                        <td>{{formatarQuantidade(itemProduto.quantidade, itemProduto)}}</td>
                        <td>{{itemProduto.custo_total | money}}</td>
                        <td width="120" class="text-center">
                            <router-link tag="span" :to="'/estoque/extrato-produto/' + itemProduto.id"
                                         class="text-bold text-info mr-2" style="cursor: pointer"
                                         v-b-tooltip.hover title="Extrato movimentações">
                                <i class="fas fa-file-alt"></i>
                            </router-link>

                            <span class="text-bold text-info mr-2" style="cursor: pointer"
                                  @click="editar({...itemProduto})"
                                  v-b-tooltip.hover title="Editar">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="text-bold text-danger" style="cursor: pointer" @click="confirmar(itemProduto)"
                                  v-b-tooltip.hover title="Apagar">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="mt-3">
                    <b-pagination
                        v-model="page"
                        :total-rows="produtos.total"
                        :per-page="produtos.per_page"
                        @input="handleLoadData"
                    ></b-pagination>
                </div>
            </div>
        </card-component>

        <b-modal size="lg" id="modal-cadastro"
                 :title="produto.hasOwnProperty('id') ? 'Editar Produto' : 'Cadastrar Produto'" hide-footer>
            <form-produtos :produto="produto" @loadData="handleLoadData"></form-produtos>
        </b-modal>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'
    import axios from "axios";
    import FormProdutos from "./FormProdutos";

    export default {
        name: "Clientes",
        components: {FormProdutos},
        async created() {
            this.load = true;
            try {
                const {data} = await this.buscarProdutos('page=1');
                this.produtos = data;
                this.load = false;
            } catch (error) {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            }
        },
        data() {
            return {
                search: '',
                load: false,
                page: 1,
                produtos: [],
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
                    {name: 'Estoque', to: null}
                ],
            }
        },
        methods: {
            ...mapActions(['buscarProdutos']),

            cadastrar() {
                this.produto = {
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
                };
                this.$bvModal.show('modal-cadastro')
            },

            editar(produto) {
                this.produto = {...produto};
                this.$bvModal.show('modal-cadastro')
            },

            confirmar(produto) {
                this.$snotify.confirm(`Tem certeza que deseja apagar o produto: ${produto.nome} ?`,
                    'Confirmar', {
                        timeout: 5000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                        buttons: [
                            {text: 'Sim', action: (toast) => this.apagar(produto, toast), bold: false},
                            {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                        ]
                    });
            },

            apagar(produto, toast) {
                this.$snotify.remove(toast.id);
                this.load = true;
                axios.delete('/api/produtos/' + produto.id).then(_ => {
                    this.$snotify.success('Produto apagado.', 'Sucesso');
                    this.handleLoadData();
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            handleLoadData(page = 1) {
                this.load = true;
                const search = this.search ? `&whereLike[nome]=${this.search}` : '';
                this.buscarProdutos(`page=${page}${search}`).then(response => {
                    this.produtos = response.data;
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                })
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
