<template>
    <page-component title="Fornecedores" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="row mb-3">
                <div class="col-sm-8">
                    <button type="button" class="btn btn-primary" @click.prevent="cadastrar">
                        <i class="fas fa-plus"></i> Cadastrar
                    </button>
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
                        <th>Nome</th>
                        <th>Contato Responsável</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="itemFornecedor in fornecedores.data">
                        <td>{{itemFornecedor.individuo.nome}}</td>
                        <td>{{itemFornecedor.individuo.contato_responsavel | celular}}</td>
                        <td>{{itemFornecedor.individuo.telefone_fixo | celular}}</td>
                        <td><status-component :status="itemFornecedor.status" /></td>
                        <td width="80" class="text-center">
                            <span class="text-bold text-info mr-2" style="cursor: pointer"
                                  @click="editar({...itemFornecedor})"
                                  v-b-tooltip.hover title="Configurações">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="text-bold text-danger" style="cursor: pointer"
                                  @click="confirmar(itemFornecedor)"
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
                        :total-rows="fornecedores.total"
                        :per-page="fornecedores.per_page"
                        @input="handleLoadData"
                    ></b-pagination>
                </div>
            </div>
        </card-component>

        <b-modal size="xl" id="modal-cadastro" :title="configuracoes ? 'Configurações' : 'Cadastro'" hide-footer>
            <form-fornecedores :estados="estados" :fornecedor="fornecedor"
                               @loadData="handleLoadData"></form-fornecedores>
        </b-modal>
    </page-component>
</template>

<script>
    import {mapActions} from "vuex";
    import axios from "axios";
    import FormFornecedores from "./FormFornecedores";

    export default {
        name: "Fornecedores",
        components: {FormFornecedores},
        async created() {
            this.load = true;
            axios.all([
                this.buscarFornecedores('with[]=individuo.individuosEndereco&page=1'),
                this.buscaEstados()
            ]).then(axios.spread((fornecedoresResponse, estadosResponse) => {
                this.fornecedores = fornecedoresResponse.data;
                this.estados = estadosResponse.data;
                this.load = false;
            })).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },
        data() {
            return {
                page: 1,
                configuracoes: false,
                search: '',
                estados: [],
                load: false,
                fornecedores: [],
                fornecedor: {
                    status: true,
                    individuo: {
                        tipo_pessoa: 'J',
                        nome: '',
                        documento: '',
                        email: '',
                        celular: '',
                        telefone_fixo: '',
                        email_contato: '',
                        responsavel: '',
                        contato_responsavel: '',
                        individuos_endereco: {
                            bairro: '',
                            cep: '',
                            cidade_id: '',
                            complemento: '',
                            estado_id: '',
                            logradouro: '',
                            numero: '',
                        }
                    }
                },
                breadcrumb: [
                    {name: 'fornecedores', to: null}
                ],
            }
        },
        methods: {
            ...mapActions(['buscarFornecedores', 'buscaEstados']),
            editar(fornecedor) {
                this.fornecedor = {...fornecedor};
                this.configuracoes = true;
                this.$bvModal.show('modal-cadastro');
            },

            cadastrar() {
                this.fornecedor = {
                    status: true,
                    individuo: {
                        tipo_pessoa: 'J',
                        nome: '',
                        documento: '',
                        email: '',
                        celular: '',
                        telefone_fixo: '',
                        email_contato: '',
                        responsavel: '',
                        contato_responsavel: '',
                        individuos_endereco: {
                            bairro: '',
                            cep: '',
                            cidade_id: '',
                            complemento: '',
                            estado_id: '',
                            logradouro: '',
                            numero: '',
                        }
                    }
                };
                this.configuracoes = false;
                this.$bvModal.show('modal-cadastro');
            },

            confirmar(fornecedor) {
                this.$snotify.confirm(`Tem certeza que deseja apagar o fornecedor: ${fornecedor.individuo.nome} ?`,
                    'Confirmar', {
                        timeout: 5000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                        buttons: [
                            {text: 'Sim', action: (toast) => this.apagar(fornecedor, toast), bold: false},
                            {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                        ]
                    });
            },

            apagar(fornecedor, toast) {
                this.$snotify.remove(toast.id);
                this.load = true;
                axios.delete('/api/fornecedores/' + fornecedor.id).then(_ => {
                    this.$snotify.success('Fornecedor apagado.', 'Sucesso');
                    this.handleLoadData();
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            handleLoadData(page = 1) {
                this.load = true;
                const search = this.search ? `&whereLikeHas[individuo]=nome,${this.search}` : '';
                this.buscarFornecedores(`with[]=individuo.individuosEndereco&page=${page}${search}`)
                    .then(response => {
                        this.fornecedores = {...response.data};
                        this.load = false;
                    }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },
        }
    }
</script>

<style scoped>

</style>
