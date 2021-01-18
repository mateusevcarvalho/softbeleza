<template>
    <page-component title="Clientes" :breadcrumb="breadcrumb">
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
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Última presença</th>
                        <th>Ticked Médio</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="itemCliente in clientes.data">
                        <td>{{itemCliente.individuo.nome}}</td>
                        <td>{{itemCliente.individuo.celular | celular}}</td>
                        <td>{{itemCliente.individuo.email_contato}}</td>
                        <td>
                            <span v-if="itemCliente.comandas.length">
                                {{itemCliente.comandas[0].data | dateTimeBr}}
                            </span>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <span v-if="itemCliente.comandas.length">
                                {{ticketMedio(itemCliente.comandas) | money}}
                            </span>
                            <span v-else>-</span>
                        </td>
                        <td width="90" class="text-center">
                            <span class="text-bold text-info mr-2" style="cursor: pointer"
                                  @click="editar({...itemCliente})"
                                  v-b-tooltip.hover title="Editar">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="text-bold text-danger" style="cursor: pointer" @click="confirmar(itemCliente)"
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
                            :total-rows="clientes.total"
                            :per-page="clientes.per_page"
                            @input="handleLoadData"
                    ></b-pagination>
                </div>
            </div>
        </card-component>

        <b-modal size="lg" id="modal-cadastro"
                 :title="cliente.hasOwnProperty('id') ? 'Editar Cliente' : 'Cadastrar Cliente'" hide-footer>
            <form-clientes :cliente="cliente" @loadData="handleLoadData"></form-clientes>
        </b-modal>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'
    import FormClientes from "./FormClientes";
    import axios from "axios";

    export default {
        name: "Clientes",
        components: {FormClientes},
        async created() {
            this.load = true;
            try {
                const {data} = await this.buscarClientes('with[]=individuo&with[]=comandas&page=1');
                this.clientes = data;
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
                clientes: [],
                cliente: {
                    nota: '',
                    individuo: {
                        nome: '',
                        celular: '',
                        email: '',
                        sexo: '',
                        documento: '',
                        data_nascimento: '',
                    }
                },
                breadcrumb: [
                    {name: 'Clientes', to: null}
                ],
            }
        },
        methods: {
            ...mapActions(['buscarClientes']),

            ticketMedio(comandas) {
                const total = comandas.reduce((a, b) => a + b.valor_pago, 0);
                const quantidadeComandas = comandas.length;
                return total / quantidadeComandas;
            },

            cadastrar() {
                this.cliente = {
                    nota: '',
                    individuo: {
                        nome: '',
                        celular: '',
                        email: '',
                        sexo: '',
                        documento: '',
                        data_nascimento: '',
                    }
                };
                this.$bvModal.show('modal-cadastro')
            },

            editar(cliente) {
                this.cliente = {...cliente};
                this.$bvModal.show('modal-cadastro')
            },

            confirmar(cliente) {
                this.$snotify.confirm(`Tem certeza que deseja apagar ocliente: ${cliente.individuo.nome} ?`,
                    'Confirmar', {
                        timeout: 5000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                        buttons: [
                            {text: 'Sim', action: (toast) => this.apagar(cliente, toast), bold: false},
                            {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                        ]
                    });
            },

            apagar(cliente, toast) {
                this.$snotify.remove(toast.id);
                this.load = true;
                axios.delete('/api/clientes/' + cliente.id).then(_ => {
                    this.$snotify.success('Cliente apagado.', 'Sucesso');
                    this.handleLoadData();
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            handleLoadData(page = 1) {
                this.load = true;
                const search = this.search ? `&whereLikeHas[individuo]=nome,${this.search}` : '';
                this.buscarClientes(`with[]=individuo&with[]=comandas&page=${page}${search}`).then(response => {
                    this.clientes = response.data;
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>
