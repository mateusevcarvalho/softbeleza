<template>
    <form @submit.prevent="onSubmit">
        <b-form-group label="Forma de Pagamento:">
            <b-form-radio-group
                    id="radio-group-1"
                    v-model="form.forma_pagamento"
                    :options="options"
                    name="radio-options"
            ></b-form-radio-group>
        </b-form-group>
        <div class="row">
            <div class="col-sm-6 form-group">
                <label>Nome <b class="text-danger">*</b></label>
                <input type="text" class="form-control" v-model="form.nome" required>
            </div>

            <div class="col-sm-6 form-group">
                <label>Tipo <b class="text-danger">*</b></label>
                <select class="form-control" v-model="form.tipo_pagamento">
                    <option value="V">Á Vista</option>
                    <option value="P">Á Prazo</option>
                </select>
            </div>

            <div class="col-sm-4 form-group mt-4">
                <label>Taxa Operadora</label>
                <money class="form-control" v-bind="percent" v-model="form.taxa_operadora"></money>
            </div>

            <div class="col-sm-4 form-group">
                <label class="text-center">Repasse da operadora (dias)</label>
                <input type="number" class="form-control" v-model="form.dias_repasse_operadora">
            </div>

            <div class="col-sm-4 form-group" v-if="form.tipo_pagamento === 'P'">
                <label class="text-center">N˚ máximo de parcelas</label>
                <input type="number" class="form-control" v-model="form.max_parcelas"
                       :class="{ 'is-invalid': form.errors.has('max_parcelas') }">

                <has-error :form="form" field="max_parcelas"></has-error>
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
    import {Form} from 'vform'

    export default {
        name: "FormMeioPagamento",
        props: {
            meiosPagamento: {
                required: true
            }
        },
        data() {
            return {
                load: false,
                options: [
                    {text: 'Dinheiro', value: 'D'},
                    {text: 'Cartão', value: 'C'}
                ],
                form: new Form({...this.meiosPagamento}),
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
            onSubmit() {
                this.load = true;
                if (this.form.id) {
                    this.form.put(`/api/meios-pagamentos/${this.form.id}`).then(_ => {
                        this.$snotify.success('Meio pagamento alterado.', 'Sucesso');
                        this.$emit('loadData');
                        this.$bvModal.hide('modal-cadastro');
                        this.load = false;
                    }).catch(error => {
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                } else {
                    this.form.post('/api/meios-pagamentos').then(_ => {
                        this.$snotify.success('Meio pagamento cadastrado.', 'Sucesso');
                        this.$emit('loadData');
                        this.$bvModal.hide('modal-cadastro');
                        this.load = false;
                    }).catch(error => {
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                }
            }
        }
    }
</script>

<style scoped>

</style>
