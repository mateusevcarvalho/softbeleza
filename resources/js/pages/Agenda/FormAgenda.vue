<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="form-group col-sm-6">
                <label>Serviço <b class="text-danger">*</b></label>
                <v-select :options="dataServicos"
                          placeholder="-Selecione-"
                          :reduce="servico => servico.id" label="nome"
                          v-model="form.servico_id">
                    <template #search="{attributes, events}">
                        <input class="vs__search" :required="!form.servico_id" v-bind="attributes"
                               v-on="events"/>
                    </template>
                    <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                </v-select>
            </div>

            <div class="form-group col-sm-3">
                <label>Horários</label>
                <input type="time" class="form-control" v-model="form.inicio" @change="onChangeHoraInicio" required>
            </div>

            <div class="form-group col-sm-3" style="margin-top: 32px">
                <input type="time" class="form-control" v-model="form.fim" required>
            </div>

            <div class="col-sm-7 form-group">
                <label>Cliente</label>
                <v-select :options="clientes"
                          :disabled="!!this.form.id"
                          placeholder="-Selecione-"
                          @input="onChangeCliente"
                          :reduce="cliente => cliente.id"
                          :getOptionLabel="cliente => cliente.individuo.nome"
                          v-model="form.cliente_id">
                    <template #search="{attributes, events}">
                        <input class="vs__search" :required="!form.cliente_id" v-bind="attributes"
                               v-on="events"/>
                    </template>
                    <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                </v-select>
            </div>

            <div class="col-sm-5 form-group">
                <label>Telefone</label>
                <input type="text" class="form-control" v-model="form.telefone" required
                       v-mask="['(##) ####-####', '(##) #####-####']">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-cadastro')">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-danger" v-if="form.hasOwnProperty('id')"
                        @click.prevent="confirmarCancelamentoHorario">
                    <i class="fas fa-calendar-times"></i> Cancelar Horário
                </button>
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
</template>

<script>
    import lodash from "lodash";
    import {mapActions} from 'vuex'
    import moment from 'moment'
    import axios from 'axios'

    export default {
        name: "FormAgenda",
        props: ['agendamento', 'servicos', 'profissionalServicos', 'clientes'],
        created() {
            if (this.agendamento.hasOwnProperty('cliente')) {
                this.form.inicio = moment(this.form.inicio).format('HH:mm');
                this.form.fim = moment(this.form.fim).format('HH:mm');
                this.form = {...this.form, data: this.agendamento.inicio};
            }
        },
        data() {
            return {
                load: false,
                form: {...this.agendamento}
            }
        },
        computed: {
            dataServicos() {
                const servicos = [];
                this.servicos.forEach(servico => {
                    if (this.profissionalServicos.find(profServico => profServico.id == servico.id)) {
                        servicos.push(servico)
                    }
                });

                return servicos;
            }
        },
        methods: {
            ...mapActions(["buscarClientes"]),

            confirmarCancelamentoHorario() {
                const cliente = this.clientes.find(cliente => cliente.id === this.form.cliente_id);
                this.$snotify.confirm(`Tem certeza que deseja cancelar o horário com o(a) cliente: ${cliente.individuo.nome} ?`,
                    'Confirmar', {
                        timeout: 5000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                        buttons: [
                            {text: 'Sim', action: (toast) => this.cancelarHorario(toast), bold: false},
                            {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                        ]
                    });
            },

            cancelarHorario(toast) {
                this.$snotify.remove(toast.id);
                this.load = true;
                axios.delete('/api/agendamentos/' + this.form.id).then(_ => {
                    this.$snotify.success('Horário cancelado.', 'Sucesso');
                    this.$bvModal.hide('modal-cadastro');
                    this.$emit('loadData');
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });

            },

            onSubmit() {
                this.load = true;
                const formData = {...this.form};
                formData.inicio = moment(this.form.data).format('YYYY-MM-DD') + ` ${formData.inicio}:00`;
                formData.fim = moment(this.form.data).format('YYYY-MM-DD') + ` ${formData.fim}:00`;

                if (this.form.hasOwnProperty('id')) {
                    axios.put('/api/agendamentos/' + this.form.id, formData).then(_ => {
                        this.$snotify.success('Agendamento salvo', 'Sucesso');
                        this.$bvModal.hide('modal-cadastro');
                        this.$emit('loadData');
                        this.load = false;
                    }).catch(error => {
                        if (error.response.status === 400) {
                            this.$snotify.warning('O profissional já possui hora marcada para este horário.', 'Alerta');
                        }
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                } else {
                    axios.post('/api/agendamentos', formData).then(_ => {
                        this.$snotify.success('Agendamento salvo', 'Sucesso');
                        this.$bvModal.hide('modal-cadastro');
                        this.$emit('loadData');
                        this.load = false;
                    }).catch(error => {
                        if (error.response.status === 400) {
                            this.$snotify.warning('O profissional já possui hora marcada para este horário.', 'Alerta');
                        }
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                }
            },

            onChangeHoraInicio() {
                const horaFim = moment(this.form.inicio, 'HH:mm').add(30, 'm');
                this.form.fim = horaFim.format('HH:mm');
            },

            onChangeCliente() {
                const cliente = this.clientes.find(cliente => cliente.id === this.form.cliente_id);
                this.form.telefone = cliente.individuo.celular;
            },
        }
    }
</script>

<style scoped>

</style>
