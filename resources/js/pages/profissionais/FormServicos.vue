<template>
    <div>
        <form @submit.prevent="onSubmit">
            <div class="row">
                <div class="form-group col-sm-4">
                    <label>Servico <b class="text-danger">*</b></label>
                    <v-select :options="selectServicos"
                              @input="servicoOnChange"
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
                <div class="form-group col-sm-2">
                    <label>Duração</label>
                    <input type="time" class="form-control" v-model="form.duracao" required>
                </div>
                <div class="form-group col-sm-3">
                    <label>Preço </label>
                    <money class="form-control" v-model="form.valor"></money>
                </div>

                <div class="form-group col-sm-3">
                    <label>Rateio </label>
                    <money class="form-control" v-bind="percent" v-model="form.rateio"></money>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 text-right">
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

        <table class="table table-striped table-bordered mt-3">
            <thead>
            <tr>
                <th>Servico</th>
                <th>Duracao</th>
                <th>Valor</th>
                <th>Rateio</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="itemServico in profissionalServicos">
                <td>{{itemServico.nome}}</td>
                <td>{{itemServico.pivot.duracao | time}}</td>
                <td>{{itemServico.pivot.valor | money}}</td>
                <td>{{itemServico.pivot.rateio | percent}}</td>
                <td width="30" class="text-center">
                    <span class="text-bold text-danger" style="cursor: pointer"
                          @click="confirmar(itemServico)" v-b-tooltip.hover title="Apagar">
                                <i class="fas fa-trash"></i>
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "FormServicos",
        props: {
            servicos: {
                required: true
            },
            profissional: {
                required: true
            }
        },
        data() {
            return {
                load: false,
                form: {
                    duracao: '',
                    valor: '',
                    rateio: '',
                    profissional_id: '',
                    servico_id: ''
                },
                percent: {
                    decimal: ',',
                    thousands: '.',
                    prefix: '',
                    suffix: ' %',
                    precision: 2,
                    masked: false
                }
            }
        },
        methods: {
            servicoOnChange() {
                const servico = this.servicos.find(servico => servico.id === this.form.servico_id);
                this.form = {...this.form, duracao: servico.duracao, valor: servico.valor, rateio: servico.rateio};
            },

            onSubmit() {
                this.load = true;
                this.form.profissional_id = this.profissional.id;
                axios.post('/api/profissionais/servicos', this.form).then(_ => {
                    this.$snotify.success('Serviço atribuido', 'Sucesso');
                    this.form = {
                        duracao: '',
                        valor: '',
                        rateio: '',
                        profissional_id: this.profissional.id,
                        servico_id: ''
                    };
                    this.$emit('loadData');
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            confirmar(servico) {
                this.$snotify.confirm(`Tem certeza que deseja remover o servico: ${servico.nome} ?`,
                    'Confirmar', {
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
                axios.delete('/api/profissionais/servicos/' + this.profissional.id + `?servico_id=${servico.id}`)
                    .then(_ => {
                        this.$snotify.success('Servico removido do profissional.', 'Sucesso');
                        this.$emit('loadData');
                        this.load = false;
                    }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },
        },
        computed: {
            profissionalServicos() {
                return this.profissional.servicos;
            },

            selectServicos() {
                const newServicos = [];
                this.servicos.forEach(servico => {
                    if (!this.profissionalServicos.find(item => item.id === servico.id)) {
                        newServicos.push(servico);
                    }
                });

                return newServicos;
            }
        }
    }
</script>

<style scoped>

</style>
