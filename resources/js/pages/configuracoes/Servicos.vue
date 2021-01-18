<template>
    <page-component title="Serviços" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="row mb-3">
                <div class="col-sm-8">
                    <button type="button" class="btn btn-primary" @click.prevent="cadastrar">
                        <i class="fas fa-plus"></i> Cadastrar
                    </button>
                </div>
                <div class="col-sm-4">
                    <b-input-group>
                        <b-form-input placeholder="Procure por nome ou cód." @keypress.enter="handleLoadData()"
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
                        <th>Cód.</th>
                        <th>Nome</th>
                        <th>Duração</th>
                        <th>Preço Base</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="servico in servicos.data">
                        <td>{{servico.codigo}}</td>
                        <td>{{servico.nome}}</td>
                        <td>{{servico.duracao | time}}</td>
                        <td>{{servico.valor | money}}</td>
                        <td><status-component :status="servico.status" /></td>
                        <td width="90" class="text-center">
                            <span class="text-bold text-info mr-2" style="cursor: pointer" @click="editar(servico)"
                                  v-b-tooltip.hover title="Configurações">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="text-bold text-danger" style="cursor: pointer" @click="confirmar(servico)"
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
                        :total-rows="servicos.total"
                        :per-page="servicos.per_page"
                        @input="handleLoadData"
                    ></b-pagination>
                </div>
            </div>
        </card-component>

        <b-modal size="lg" id="modal-cadastro" :title="configuracoes ? 'Configurações' : 'Cadastro'"
                 hide-footer>
            <form-servico :categorias="categorias" :codigo="codigo"
                          :servico="servico" @loadData="handleLoadData"></form-servico>
        </b-modal>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'
    import axios from 'axios'
    import FormServico from "./FormServico";
    import moment from 'moment'

    export default {
        name: "Servicos",
        components: {FormServico},
        created() {
            this.load = true;
            axios.all([
                this.buscarServicos('page=1'),
                this.buscarServicoCategorias()
            ]).then(axios.spread((servicoResponse, categoriasResponse) => {
                this.servicos = servicoResponse.data;
                this.categorias = categoriasResponse.data;
                this.load = false;
            })).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            })
        },
        data() {
            return {
                configuracoes: false,
                page: 1,
                search: '',
                load: false,
                servicos: [],
                formDefault: {
                    servico_categoria_id: '',
                    codigo: this.codigo,
                    nome: '',
                    valor: '',
                    duracao: '',
                    rateio: '',
                    descricao: '',
                    possui_desconto: false,
                    data_desconto_inicio: null,
                    data_desconto_fim: null,
                    hora_inicio: null,
                    hora_fim: null,
                    valor_desconto: '',
                    valor_sob_consulta: false,
                    agenda_online: true,
                    status: true
                },
                servico: this.formDefault,
                categorias: [],
                breadcrumb: [
                    {name: 'Configurações', to: null},
                    {name: 'Serviços', to: null},
                ],
            }
        },
        methods: {
            ...mapActions(['buscarServicoCategorias', 'buscarServicos']),
            editar(servico) {

                const data_desconto_inicio = servico.data_desconto_inicio ?
                    moment(servico.data_desconto_inicio).format('YYYY-MM-DD') : '';

                const data_desconto_fim = servico.data_desconto_fim ?
                    moment(servico.data_desconto_fim).format('YYYY-MM-DD') : '';

                const hora_inicio = servico.data_desconto_inicio ?
                    moment(servico.data_desconto_inicio).format('HH:mm') : '';

                const hora_fim = servico.data_desconto_fim ?
                    moment(servico.data_desconto_fim).format('HH:mm') : '';

                this.servico = {...servico, data_desconto_inicio, data_desconto_fim, hora_inicio, hora_fim};
                this.configuracoes = true;
                this.$bvModal.show('modal-cadastro');
            },

            cadastrar() {
                this.servico = {...this.formDefault, codigo: this.codigo};
                this.configuracoes = false;
                this.$bvModal.show('modal-cadastro');
            },

            confirmar(servico) {
                this.$snotify.confirm(`Tem certeza que deseja apagar o serviço: ${servico.nome} ?`, 'Confirmar', {
                    timeout: 5000,
                    showProgressBar: true,
                    closeOnClick: true,
                    pauseOnHover: true,
                    buttons: [
                        {text: 'Sim', action: (toast) => this.apagar(servico, toast), bold: false},
                        {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                    ]
                });
            },

            apagar(servico, toast) {
                this.$snotify.remove(toast.id);
                this.load = true;
                axios.delete('/api/servicos/' + servico.id).then(_ => {
                    this.$snotify.success('Serviço apagado.', 'Sucesso');
                    this.handleLoadData();
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            handleLoadData(page = 1) {
                this.load = true;
                const search = this.search ? `&whereLike[nome]=${this.search}&orWhere[codigo]=${this.search}&where[status]=A` : '';
                this.buscarServicos(`page=${page}${search}`).then(response => {
                    this.servicos = response.data;
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                })
            }
        },
        computed: {
            codigo() {
                const tamanho = this.servicos.total;
                if (tamanho === 0) {
                    return '01';
                } else if ((tamanho + 1) < 10) {
                    return '0' + (tamanho + 1)
                } else {
                    return tamanho + 1
                }
            }
        }
    }
</script>

<style scoped>

</style>
