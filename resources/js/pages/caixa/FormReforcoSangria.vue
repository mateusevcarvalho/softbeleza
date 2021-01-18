<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col form-group">
                <label>Valor</label>
                <money class="form-control" v-model="form.valor_pago"></money>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click.prevent="onclose">
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
    import moment from 'moment'

    export default {
        name: "FormReforcoSangria",
        props: ['caixa', 'tipo'],
        created() {

        },
        data() {
            return {
                load: false,
                form: {
                    caixa_id: this.caixa.id,
                    tipo: this.tipo,
                    valor_pago: '',
                    valor_total: '',
                    status: 'F'
                }
            }
        },
        watch: {
            'form.valor_pago': function (newValue) {
                this.form.valor_total = newValue;
            }
        },
        methods: {
            onSubmit() {
                this.load = true;
                const formData = {...this.form, data: moment().format('YYYY-MM-DD HH:mm:ss')};
                axios.post('/api/comandas', formData).then(_ => {
                    if(formData.tipo === 'R') {
                        this.$snotify.success('Reforço realizado', 'Sucesso');
                    } else {
                        this.$snotify.success('Saque realizado', 'Sucesso');
                    }
                    this.$bvModal.hide('modal-reforco');
                    this.$bvModal.hide('modal-saque');
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            onclose() {
                this.$bvModal.hide('modal-reforco');
                this.$bvModal.hide('modal-saque');
            }
        }
    }
</script>

<style scoped>

</style>
