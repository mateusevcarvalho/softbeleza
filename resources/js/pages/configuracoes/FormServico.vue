<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
<!--            <div class="mb-3 col-sm-10 d-flex">
                <div>
                    <b-form-checkbox id="checkbox-1" v-model="form.possui_desconto" :value="true"
                                     @change="limparDesconto" :unchecked-value="false">
                        Definir desconto
                    </b-form-checkbox>
                </div>
            </div>-->

            <div class="col-sm-12 mb-3">
                <b-form-checkbox v-model="form.status" name="check-button" switch>
                    <span v-if="form.status">Ativo</span>
                    <span v-else>Inativo</span>
                </b-form-checkbox>
            </div>

            <div class="form-group col-sm-2">
                <label>Código</label>
                <input type="text" class="form-control" v-model="form.codigo" required
                       :class="{ 'is-invalid': form.errors.has('codigo') }">
                <has-error :form="form" field="codigo"></has-error>
            </div>

            <div class="form-group col-sm-6">
                <label>Nome</label>
                <input type="text" class="form-control" v-model="form.nome"
                       :class="{ 'is-invalid': form.errors.has('nome') }" required>
                <has-error :form="form" field="nome"></has-error>
            </div>

            <div class="form-group col-sm-4">
                <label>Preço Base</label>
                <money class="form-control" v-model="form.valor"></money>
            </div>

            <div class="col-sm-5">
                <div class="form-group">
                    <label>Categoria</label>
                    <v-select :options="categorias"
                              placeholder="-Selecione-"
                              :reduce="categoria => categoria.id" label="nome"
                              v-model="form.servico_categoria_id">
                        <template #search="{attributes, events}">
                            <input class="vs__search" :required="!form.servico_categoria_id" v-bind="attributes"
                                   v-on="events"/>
                        </template>
                        <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                    </v-select>
                </div>
                <has-error :form="form" field="servico_categoria_id"></has-error>
            </div>

            <div class="form-group col-sm-3">
                <label>Duração</label>
                <input type="time" class="form-control" v-model="form.duracao" required
                       :class="{ 'is-invalid': form.errors.has('duracao') }">
                <has-error :form="form" field="duracao"></has-error>
            </div>

            <div class="form-group col-sm-4">
                <label>Rateio</label>
                <money class="form-control" v-bind="percent" v-model="form.rateio"></money>
            </div>

            <div class="form-group col-sm-8"></div>

            <div class="form-group col-sm-12">
                <label>Descrição</label>
                <textarea v-model="form.descricao" class="form-control"
                          :class="{ 'is-invalid': form.errors.has('descricao') }"></textarea>
                <has-error :form="form" field="descricao"></has-error>
            </div>

            <div class="form-group col-sm-3" v-if="form.possui_desconto">
                <label>Data Desconto Inicio</label>
                <input type="date" class="form-control" v-model="form.data_desconto_inicio"
                       :class="{ 'is-invalid': form.errors.has('data_desconto_inicio') }">
                <has-error :form="form" field="data_desconto_inicio"></has-error>
            </div>

            <div class="form-group col-sm-2" v-if="form.possui_desconto">
                <label>Hora inicio</label>
                <input type="time" class="form-control" v-model="form.hora_inicio"
                       :class="{ 'is-invalid': form.errors.has('hora_inicio') }">
                <has-error :form="form" field="hora_inicio"></has-error>
            </div>

            <div class="form-group col-sm-3" v-if="form.possui_desconto">
                <label>Data Desconto Fim</label>
                <input type="date" class="form-control" v-model="form.data_desconto_fim"
                       :class="{ 'is-invalid': form.errors.has('data_desconto_fim') }">
                <has-error :form="form" field="data_desconto_fim"></has-error>
            </div>

            <div class="form-group col-sm-2" v-if="form.possui_desconto">
                <label>Hora Fim</label>
                <input type="time" class="form-control" v-model="form.hora_fim"
                       :class="{ 'is-invalid': form.errors.has('hora_fim') }">
                <has-error :form="form" field="hora_fim"></has-error>
            </div>

            <div class="form-group col-sm-2" v-if="form.possui_desconto">
                <label>Vlr. Desconto</label>
                <money class="form-control" v-model="form.valor_desconto"
                       :class="{ 'is-invalid': form.errors.has('valor_desconto') }"></money>
                <has-error :form="form" field="valor_desconto"></has-error>
            </div>

<!--            <div class="col-sm-6">
                <b-form-checkbox id="checkbox-3" v-model="form.agenda_online" :value="true"
                                 :unchecked-value="false">
                    Cliente pode agendar online
                </b-form-checkbox>
                <b-form-checkbox id="checkbox-2" v-model="form.valor_sob_consulta" :value="true"
                                 :unchecked-value="false">
                    Preço sob consulta na agenda online
                </b-form-checkbox>
            </div>-->
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-cadastro')">
                    <i class="fas fa-times"></i> Fechar
                </button>
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
</template>

<script>
    import axios from 'axios'
    import {Form} from 'vform'

    export default {
        name: "FormServico",
        props: ['categorias', 'codigo', 'servico'],
        data() {
            return {
                load: false,
                form: new Form({...this.servico}),
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
            limparDesconto() {
                if (!this.form.possui_desconto) {
                    this.form.data_desconto_inicio = '';
                    this.form.data_desconto_fim = '';
                    this.form.hora_inicio = '';
                    this.form.hora_fim = '';
                    this.form.valor_desconto = '';
                }
            },
            onSubmit() {
                this.load = true;
                if (this.form.hasOwnProperty('id')) {
                    this.form.put('/api/servicos/' + this.form.id).then(_ => {
                        this.$snotify.success('Serviço editado.', 'Sucesso');
                        this.$bvModal.hide('modal-cadastro');
                        this.$emit('loadData');
                        this.load = false;
                    }).catch(error => {
                        this.$snotify.error('Falha interna, favor tente mais tarde.', 'Erro');
                        this.load = false;
                    })
                } else {
                    this.form.post('/api/servicos').then(_ => {
                        this.$snotify.success('Serviço cadastrado.', 'Sucesso');
                        this.$bvModal.hide('modal-cadastro');
                        this.$emit('loadData');
                        this.load = false;
                    }).catch(error => {
                        this.$snotify.error('Falha interna, favor tente mais tarde.', 'Erro');
                        this.load = false;
                    })
                }

            }
        },
    }
</script>

<style scoped>

</style>
