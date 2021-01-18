<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col-sm-6 form-group">
                <label>Nome Completo<b class="text-danger">*</b></label>
                <input type="text" class="form-control" v-model="form.individuo.nome" required>
            </div>

            <div class="col-sm-6 form-group">
                <label>Telefone</label>
                <input type="text" class="form-control" v-model="form.individuo.celular"
                       v-mask="['(##) ####-####', '(##) #####-####']">
            </div>

            <div class="col-sm-6 form-group">
                <label>Email</label>
                <input type="email" class="form-control" v-model="form.individuo.email_contato">
            </div>

            <div class="form-group col-sm-6">
                <label>Sexo</label>
                <select v-model="form.individuo.sexo" class="form-control">
                    <option value="">-Selecione-</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label>CPF</label>
                <input type="text" class="form-control" v-model="form.individuo.documento" v-mask="'###.###.###-##'">
            </div>

            <div class="form-group col-sm-6">
                <label>Data Nascimento</label>
                <input type="date" class="form-control" v-model="form.individuo.data_nascimento">
            </div>

            <div class="form-group col-sm-12">
                <label>Nota (Não visível para o cliente)</label>
                <textarea class="form-control" v-model="form.nota"></textarea>
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
    import axios from 'axios'

    export default {
        name: "FormClientes",
        props: {
            cliente: {
                required: true
            }
        },
        data() {
            return {
                load: false,
                form: {...this.cliente},
            }
        },

        methods: {
            onSubmit() {
                this.load = true;
                if (this.form.id) {
                    axios.put(`/api/clientes/${this.form.id}`, this.form).then(_ => {
                        this.$snotify.success('Cliente alterado.', 'Sucesso');
                        this.$emit('loadData');
                        this.$bvModal.hide('modal-cadastro');
                        this.load = false;
                    }).catch(error => {
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                } else {
                    axios.post('/api/clientes', this.form).then(_ => {
                        this.$snotify.success('Cliente cadastrado.', 'Sucesso');
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
