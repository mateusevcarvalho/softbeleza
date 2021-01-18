<template>
    <form @submit.prevent="onSubmit">
        <h4>Abertura de Caixa</h4>
        <div class="text-center">
            <h5>Informe os valores disponíveis para iniciar suas operações</h5>
            <p class="mt-5 text-secondary"><i class="fas fa-cash-register fa-7x"></i></p>
        </div>
        <div class="row text-center mt-5">
            <div class="col"></div>
            <div class="col form-group">
                <label>Disponível em caixa</label>
                <money v-model="form.saldo_inicial" class="form-control form-control-lg form-control"/>
            </div>
            <div class="col"></div>
        </div>
        <div class="row text-center mt-2">
            <div class="col"></div>
            <div class="col">
                <button type="submit" class="btn btn-lg btn-primary btn-block" :disabled="load">
                    <span v-if="load">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </span>
                    <span v-else><i class="fas fa-sign-in-alt"></i></span>
                    Confirma Abertura
                </button>
            </div>
            <div class="col"></div>
        </div>
    </form>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "FormAbertura",
        data() {
            return {
                load: false,
                form: {
                    saldo_inicial: 0,
                }
            }
        },

        methods: {
            onSubmit() {
                this.load = true;
                axios.post('/api/caixas', this.form).then(_ => {
                    this.$snotify.success('Caixa aberto.', 'Sucesso');
                    this.$emit('loadCaixa');
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
