<template>
    <page-component title="Profissionais" :breadcrumb="breadcrumb">
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
                        <th>Apelido</th>
                        <th>Celular</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="itemProfissional in profissionais.data">
                        <td>{{ itemProfissional.individuo.nome }}</td>
                        <td>{{ itemProfissional.individuo.apelido }}</td>
                        <td>{{ itemProfissional.individuo.celular | celular }}</td>
                        <td>
                            <status-component :status="itemProfissional.status"/>
                        </td>
                        <td width="130" class="text-center">
                            <span class="text-bold text-info mr-2" style="cursor: pointer"
                                  @click="modalServicos({...itemProfissional})"
                                  v-b-tooltip.hover title="Serviços">
                                <i class="fas fa-cut"></i>
                            </span>
                            <span class="text-bold text-info mr-2" style="cursor: pointer"
                                  @click="horarioDeTrabalho({...itemProfissional})"
                                  v-b-tooltip.hover title="Horário de trabalho">
                                <i class="fas fa-clock"></i>
                            </span>
                            <span class="text-bold text-info mr-2" style="cursor: pointer"
                                  @click="editar({...itemProfissional})"
                                  v-b-tooltip.hover title="Configurações">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="text-bold text-danger" style="cursor: pointer"
                                  @click="confirmar(itemProfissional)"
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
                        :total-rows="profissionais.total"
                        :per-page="profissionais.per_page"
                        @input="handleLoadData"
                    ></b-pagination>
                </div>
            </div>
        </card-component>

        <b-modal size="lg" id="modal-cadastro" :title="configuracoes ? 'Configurações' : 'Cadastro'" hide-footer>
            <form-profissionais
                :controle-acesso="controleAcesso"
                :profissional="profissional"
                @loadData="handleLoadData"/>
        </b-modal>

        <b-modal size="xl" id="modal-servicos"
                 :title="'Serviços: ' + profissional.individuo.nome" hide-footer>
            <form-servicos
                :servicos="servicos"
                :profissional="profissional"
                @loadData="handleLoadData"/>
        </b-modal>

        <b-modal size="xl" id="modal-horarios" :title="'Horários de trabalho: ' + profissional.individuo.nome"
                 hide-footer>
            <form-horarios-trabalho
                :form-dias="horarios"
                @loadData="handleLoadData"/>
        </b-modal>
    </page-component>
</template>

<script>
import {mapActions} from "vuex";
import axios from "axios";
import FormProfissionais from "./FormProfissionais";
import FormHorariosTrabalho from "./FormHorariosTrabalho";
import FormServicos from "./FormServicos";

export default {
    name: "Profissionais",
    components: {FormServicos, FormHorariosTrabalho, FormProfissionais},
    async created() {
        this.load = true;
        axios.all([
            this.buscarProfissionais('with[]=individuo&with[]=usuario.controleAcessos&with[]=horarioAtendimentoProfissional&with[]=servicos&page=1'),
            this.buscarServicos('where[status]=A'),
            this.buscarControleAcesso(),
        ]).then(axios.spread((profissionaisResponse, servicosResponse, controleAcessoResponse) => {
            this.profissionais = {...profissionaisResponse.data};
            this.servicos = servicosResponse.data;
            this.controleAcesso = controleAcessoResponse.data;
            this.load = false;
        })).catch(error => {
            this.$store.dispatch('handleCatchError', error);
            this.load = false;
        })
    },
    data() {
        return {
            page: 1,
            configuracoes: false,
            search: '',
            servicos: [],
            controleAcesso: [],
            load: false,
            profissionais: [],
            formDefault: {
                descontar_taxas_rateio: true,
                avatar: '',
                status: true,
                individuo: {
                    tipo_pessoa: 'F',
                    nome: '',
                    documento: '',
                    data_nascimento: '',
                    email: '',
                    apelido: '',
                    sexo: '',
                    celular: ''
                }
            },
            profissional: {
                descontar_taxas_rateio: true,
                avatar: '',
                status: true,
                individuo: {
                    tipo_pessoa: 'F',
                    nome: '',
                    documento: '',
                    data_nascimento: '',
                    email: '',
                    apelido: '',
                    sexo: '',
                    celular: ''
                }
            },
            horarios: {
                profissional_id: '',
                dias: [
                    {dia: 1, name: 'Domingo', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 2, name: 'Segunda', horarios: []},
                    {dia: 3, name: 'Terça', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 4, name: 'Quarta', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 5, name: 'Quinta', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 6, name: 'Sexta', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 7, name: 'Sábado', horarios: [{hora_inicio: null, hora_fim: null}]}
                ],
            },
            breadcrumb: [
                {name: 'Profissionais', to: null}
            ],
        }
    },
    methods: {
        ...mapActions(['buscarProfissionais', 'buscarServicos', 'buscarControleAcesso']),
        editar(profissional) {
            this.profissional = {...profissional};
            this.configuracoes = true;
            this.$bvModal.show('modal-cadastro');
        },

        cadastrar() {
            this.profissional = {...this.formDefault};
            this.configuracoes = false;
            this.$bvModal.show('modal-cadastro');
        },

        modalServicos(profissional) {
            this.profissional = {...profissional};
            this.$bvModal.show('modal-servicos');
        },

        horarioDeTrabalho(profissional) {
            this.profissional = {...profissional};
            this.montarData(profissional.horario_atendimento_profissional, profissional.id);
            this.$bvModal.show('modal-horarios');
        },

        confirmar(profissional) {
            this.$snotify.confirm(`Tem certeza que deseja apagar o profissional: ${profissional.individuo.nome} ?`,
                'Confirmar', {
                    timeout: 5000,
                    showProgressBar: true,
                    closeOnClick: true,
                    pauseOnHover: true,
                    buttons: [
                        {text: 'Sim', action: (toast) => this.apagar(profissional, toast), bold: false},
                        {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                    ]
                });
        },

        apagar(profissional, toast) {
            this.$snotify.remove(toast.id);
            this.load = true;
            axios.delete('/api/profissionais/' + profissional.id).then(_ => {
                this.$snotify.success('Profissional apagado.', 'Sucesso');
                this.handleLoadData();
            }).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },

        handleLoadData(page = 1) {
            this.load = true;
            const search = this.search ? `&whereLikeHas[individuo]=nome,${this.search}` : '';
            this.buscarProfissionais(`with[]=individuo&with[]=usuario.controleAcessos&with[]=horarioAtendimentoProfissional&with[]=servicos&page=${page}${search}`)
                .then(response => {
                    this.profissionais = {...response.data};
                    if (this.profissional.id) {
                        this.profissional = {...response.data.data.find(prof => prof.id === this.profissional.id)};
                    }
                    this.load = false;
                }).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },

        montarData(data, profissionalId) {
            this.horarios.profissional_id = profissionalId;
            if (data.length) {
                this.horarios.dias.forEach((dia, index) => {
                    this.horarios.dias[index].horarios = [];
                    data.forEach(item => {
                        if (parseInt(item.dia) === parseInt(dia.dia)) {
                            this.horarios.dias[index].horarios.push({
                                hora_inicio: item.hora_inicio,
                                hora_fim: item.hora_fim
                            });
                        }
                    });
                });
            } else {
                this.horarios = {
                    profissional_id: profissionalId, dias: [
                        {dia: 1, name: 'Domingo', horarios: [{hora_inicio: null, hora_fim: null}]},
                        {dia: 2, name: 'Segunda', horarios: []},
                        {dia: 3, name: 'Terça', horarios: [{hora_inicio: null, hora_fim: null}]},
                        {dia: 4, name: 'Quarta', horarios: [{hora_inicio: null, hora_fim: null}]},
                        {dia: 5, name: 'Quinta', horarios: [{hora_inicio: null, hora_fim: null}]},
                        {dia: 6, name: 'Sexta', horarios: [{hora_inicio: null, hora_fim: null}]},
                        {dia: 7, name: 'Sábado', horarios: [{hora_inicio: null, hora_fim: null}]}
                    ]
                };
            }
        },
    }
}
</script>

<style scoped>

</style>
