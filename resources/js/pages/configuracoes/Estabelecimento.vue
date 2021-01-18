<template>
    <page-component title="Estabelecimento" :breadcrumb="breadcrumb">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
                                   href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                                   aria-selected="true"> <i class="fas fa-file-alt"></i> Dados Gerais</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                                   href="#custom-tabs-three-profile" role="tab"
                                   aria-controls="custom-tabs-three-profile"
                                   aria-selected="false"><i class="fas fa-map-marker-alt"></i> Endereço</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill"
                                   href="#custom-tabs-three-messages" role="tab"
                                   aria-controls="custom-tabs-three-messages"
                                   aria-selected="false"> <i class="fas fa-clock"></i> Horário de Funcionamento</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-three-tabContent">
                            <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel"
                                 aria-labelledby="custom-tabs-three-home-tab">
                                <form-estabelecimento :form-estabelecimento="formEstabelecimento"
                                                      :tipos="tipos"
                                                      @onLoad="handleOnLoad">
                                </form-estabelecimento>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel"
                                 aria-labelledby="custom-tabs-three-profile-tab">
                                <form-endereco-estabelecimento :estados="estados"
                                                               :form-estabelecimento="formEstabelecimento"
                                                               @onLoad="handleOnLoad"
                                ></form-endereco-estabelecimento>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel"
                                 aria-labelledby="custom-tabs-three-messages-tab">
                                <form-horario-funcionamento :form-dias="dias"
                                                            @onLoad="handleOnLoad">
                                </form-horario-funcionamento>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->

                    <div class="overlay" v-if="load">
                        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </page-component>
</template>

<script>
    import FormEstabelecimento from "./FormEstabelecimento";
    import FormEnderecoEstabelecimento from "./FormEnderecoEstabelecimento";
    import FormHorarioFuncionamento from "./FormHorarioFuncionamento";
    import {mapActions} from 'vuex'
    import axios from 'axios'

    export default {
        name: "Estabelecimento",
        components: {FormHorarioFuncionamento, FormEnderecoEstabelecimento, FormEstabelecimento},
        async created() {
            this.load = true;
            const estabelecimento = localStorage.getItem('estabelecimento');
            axios.all([
                this.buscarEstabelecimento(`where[uuid]=${estabelecimento}&with[]=individuo.individuosEndereco&first=true`),
                this.buscarTiposEstabelecimento(),
                this.buscaEstados(),
                this.buscarHorariosEstabelecimentos()
            ]).then(axios.spread((responseEstabelecimento, responseTipos, responseEstados, responseHorarios) => {
                this.tipos = responseTipos.data;
                this.estados = responseEstados.data;
                this.montarData(responseHorarios.data);
                this.montarEstabelecimento(responseEstabelecimento.data);
                this.load = false;
            })).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },
        data() {
            return {
                load: false,
                breadcrumb: [{name: 'Configurações', to: null}, {name: 'Estabelecimento', to: null},],
                formEstabelecimento: {
                    id: '',
                    individuo: {
                        nome: '',
                        razao_social: '',
                        documento: '',
                        email_contato: '',
                        celular: '',
                        telefone_fixo: '',
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
                    },
                },
                dias: [
                    {dia: 1, name: 'Domingo', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 2, name: 'Segunda', horarios: []},
                    {dia: 3, name: 'Terça', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 4, name: 'Quarta', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 5, name: 'Quinta', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 6, name: 'Sexta', horarios: [{hora_inicio: null, hora_fim: null}]},
                    {dia: 7, name: 'Sábado', horarios: [{hora_inicio: null, hora_fim: null}]}
                ],
                tipos: [],
                estados: []
            }
        },
        methods: {
            ...mapActions(['buscarTiposEstabelecimento', 'buscarEstabelecimento', 'buscaEstados',
                'buscarHorariosEstabelecimentos']),

            handleOnLoad(load) {
                this.load = load;
            },

            montarEstabelecimento(responseEstablecimento) {
                this.formEstabelecimento = {...responseEstablecimento};
            },

            montarData(data) {
                if (data.length) {
                    this.dias.forEach((dia, index) => {
                        this.dias[index].horarios = [];
                        data.forEach(item => {
                            if (parseInt(item.dia) === parseInt(dia.dia)) {
                                this.dias[index].horarios.push({
                                    hora_inicio: item.hora_inicio,
                                    hora_fim: item.hora_fim
                                });
                            }
                        });
                    });
                }
            },
        }
    }
</script>

<style scoped>

</style>
