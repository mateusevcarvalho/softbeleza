<template>
    <page-component title="Agenda" :breadcrumb="breadcrumb">
        <div class="card">
            <vue-cal
                @cell-click="cellOnClick"
                :on-event-click="cellOnClick"
                :events="eventos"
                :split-days="splitDays"
                :sticky-split-labels="true"
                :time-from="horarioInicio"
                :time-to="horarioFim"
                :time-step="30"
                :min-split-width="250"
                @view-change="onChange"
                :disable-views="['years', 'year', 'week']"
                active-view="day"
                locale="pt-br"
                cell-contextmenu
                today-button
                style="height: 750px">
                <template v-slot:event="{ event, view }">
                    <div class="vuecal__event-title" style="font-size: small">
                        {{ event.title | firstLastName }} {{ event.start | time }} - {{ event.end | time }} <br>
                        <i class="fas fa-cut"></i> {{ event.service }}
                    </div>
                </template>
            </vue-cal>
            <div class="overlay" v-if="load">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>

        <b-modal size="lg" id="modal-cadastro" hide-footer hide-header>
            <div class="text-center">
                <div class="titulo-modal mb-2">
                    <h5>{{ titleModal }} {{ data_evento }}</h5>
                </div>
                <h6>{{ nome_profissional }}</h6>
                <hr>
            </div>

            <form-agenda
                :agendamento="agendamento"
                :servicos="servicos"
                :clientes="clientes"
                :profissional-servicos="profissionalServicos"
                @loadData="handleLoadEvents"/>
        </b-modal>
    </page-component>
</template>

<script>
import axios from 'axios'
import {mapActions} from 'vuex'
import moment from 'moment'
import 'moment/locale/pt-br'
import FormAgenda from "./FormAgenda";

export default {
    name: "Agenda",
    components: {FormAgenda},
    created() {
        this.load = true;
        const inicio = moment().format('YYYY-MM-01');
        const fim = moment().endOf('month').format('YYYY-MM-DD');
        const conditions = `whereBetween[inicio]=${inicio} 00:00:01,${fim} 23:59:59`;
        axios.all([
            this.buscarHorariosEstabelecimentos(),
            this.buscarProfissionais('with[]=individuo&with[]=servicos&where[status]=A&where[possui_agenda]=1'),
            this.buscarAgendamentos('with[]=servico&with[]=cliente.individuo&' + conditions),
            this.buscarServicos('where[status]=A'),
            this.buscarClientes('with[]=individuo'),
        ]).then(axios.spread((responseHorarios, resProfissional, resAgendamentos, resServicos, resClientes) => {
            this.horariosFuncionamento = responseHorarios.data;
            this.servicos = resServicos.data;
            this.profissionais = resProfissional.data;
            this.clientes = resClientes.data;
            this.montarEvents(resAgendamentos.data);
            this.montarSplitDays(resProfissional.data);
            this.onChange({startDate: moment(), view: 'day'});
            this.load = false;
        })).catch(error => {
            this.$store.dispatch('handleCatchError', error);
            this.load = false;
        });
    },
    data() {
        return {
            load: false,
            splitDays: [],
            profissionais: [],
            servicos: [],
            clientes: [],
            eventos: [],
            titleModal: '',
            horariosFuncionamento: [],
            horarioInicio: 8 * 60,
            horarioFim: 19 * 60,
            nome_profissional: '',
            data_evento: '',
            last_date: moment(),
            profissionalServicos: [],
            agendamento: {
                profissional_id: '',
                servico_id: '',
                cliente_id: '',
                inicio: '',
                fim: '',
                telefone: '',
                data: ''
            },
            breadcrumb: [
                {name: 'Agenda', to: null},
            ],
        }
    },
    methods: {
        ...mapActions(["buscarHorariosEstabelecimentos", "buscarProfissionais", "buscarAgendamentos",
            "buscarServicos", "buscarClientes"]),

        cellOnClick(event) {
            if (event.hasOwnProperty('split')) {
                moment.locale('pt-BR');
                const profissional = this.profissionais.find(prof => prof.id === parseInt(event.split));
                this.data_evento = moment(event.date).format('DD [De] MMMM [De] YYYY');
                this.nome_profissional = profissional.individuo.nome;
                this.profissionalServicos = profissional.servicos;

                if (event.hasOwnProperty('agendamento')) {
                    this.titleModal = 'Agendamento:';
                    this.agendamento = {...event.agendamento};
                } else {
                    this.titleModal = 'Novo Agendamento:';
                    this.agendamento = {
                        profissional_id: event.split,
                        data: event.date,
                        servico_id: '',
                        cliente_id: '',
                        inicio: '',
                        fim: '',
                        telefone: '',
                    };
                }

                this.$bvModal.show('modal-cadastro');
            }
        },

        onChange(event) {
            if (moment(event.startDate).month() !== moment().month()) {
                this.handleLoadEvents(event.startDate)
            }

            if (moment(event.startDate).month() === moment().month() &&
                moment(this.last_date).month() !== moment().month()) {
                this.handleLoadEvents(event.startDate)
            }

            this.last_date = event.startDate;
            if (event.view === 'day') {
                const horarios = [];
                const diaSemana = moment(event.startDate).weekday() + 1;
                this.horariosFuncionamento.forEach(horario => {
                    if (parseInt(horario.dia) === diaSemana) {
                        horarios.push(horario);
                    }
                });

                if (horarios.length) {
                    const horaInicio = parseInt(moment(horarios[0].hora_inicio, 'HH:mm:ss').format('HH'));
                    const minutosInicio = parseInt(moment(horarios[0].hora_inicio, 'HH:mm:ss').format('mm'));

                    const horaFim = parseInt(moment(horarios[horarios.length - 1].hora_fim, 'HH:mm:ss').format('HH'));
                    const minutosFim = parseInt(moment(horarios[horarios.length - 1].hora_fim, 'HH:mm:ss').format('mm'));

                    this.horarioInicio = ((horaInicio - 1) * 60) + minutosInicio;
                    this.horarioFim = ((horaFim + 1) * 60) + minutosFim;
                } else {
                    this.horarioInicio = 0;
                    this.horarioFim = 0;
                }
            }
        },

        montarEvents(agendamentos) {
            const events = [];
            agendamentos.forEach(agendamento => {
                events.push({
                    start: agendamento.inicio,
                    end: agendamento.fim,
                    title: agendamento.cliente.individuo.nome,
                    class: 'leisure',
                    service: agendamento.servico.nome,
                    agendamento,
                    split: agendamento.profissional_id
                },)
            });

            this.eventos = events;
        },

        montarSplitDays(profissionais) {
            const split = [];
            profissionais.forEach((prof, index) => {
                const classe = index % 2 === 0 ? 'striped_dark' : 'striped_light';
                split.push({id: prof.id, class: classe, label: this.firstLastName(prof.individuo.nome)})
            });

            this.splitDays = split;
        },

        firstLastName(value) {
            const dataNome = value.split(' ');
            const ultimoNome = dataNome.length > 1 ? ' ' + dataNome[dataNome.length - 1] : '';
            return dataNome[0] + ultimoNome;
        },

        handleLoadEvents(data = '') {
            let conditions = '';
            if (data) {
                const inicio = moment(data).format('YYYY-MM-01 00:00:00');
                const fim = moment(data).endOf('month').format('YYYY-MM-DD 23:59:59');
                conditions = `&whereBetween[inicio]=${inicio},${fim}`;
            } else {
                const inicio = moment().format('YYYY-MM-01 00:00:00');
                const fim = moment().endOf('month').format('YYYY-MM-DD 23:59:59');
                conditions = `&whereBetween[inicio]=${inicio},${fim}`;
            }

            this.load = true;
            this.buscarAgendamentos('with[]=servico&with[]=cliente.individuo' + conditions).then(response => {
                this.montarEvents(response.data);
                this.load = false;
            }).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        }
    }
}
</script>

<style scoped>
.titulo-modal {
    padding: 10px;
    background-color: rgba(0, 0, 0, .05);
}
</style>
