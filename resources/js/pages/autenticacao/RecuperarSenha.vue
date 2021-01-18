<template>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">
                Digite seu email para recuperar a senha.
            </p>

            <form @submit.prevent="cadastro" @keydown="form.onKeydown($event)">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" v-model="form.email" required
                           :class="{ 'is-invalid': form.errors.has('email') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="email"></has-error>
                </div>

                <div class="row">
                    <!-- /.col -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block" :disabled="form.busy">
                            <span v-if="form.busy">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                            <span v-else>Enviar</span>
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>


            <div class="mt-3 text-center">
                <router-link :to="{name: 'login'}" class="text-center">
                    Voltar para o login
                </router-link>
            </div>
        </div>
        <!-- /.form-box -->
    </div>
</template>

<script>
import {Form} from 'vform'

export default {
    name: "RecuperarSenha",
    data() {
        return {
            formSubmit: false,
            form: new Form({
                email: ''
            })
        }
    },
    methods: {
        cadastro() {
            this.form.post('/api/enviar-email-recuperacao-senha').then(({data}) => {
                this.formSubmit = true;
                this.$swal.fire('Email enviado!', data.msg, 'success');
                this.form.reset();
            }).catch(error => {
                if (error.hasOwnProperty('response') && error.response.status === 404) {
                    this.$swal.fire('Ooops!', 'Email informado não encontrado.', 'warning');
                } else {
                    this.$swal.fire('Ooops!', 'Sistema instável no momento, tente mais tarde!', 'error');
                }
            });
        }
    },
}
</script>

<style scoped>

</style>
