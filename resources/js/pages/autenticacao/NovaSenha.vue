<template>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">
                Digite os dados da nova senha.
            </p>

            <form @submit.prevent="cadastro" @keydown="form.onKeydown($event)">
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Senha" v-model="form.password" required
                           :class="{ 'is-invalid': form.errors.has('password') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="email"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Confirmar Senha"
                           v-model="form.confirm_password" required
                           :class="{ 'is-invalid': form.errors.has('password') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
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
        </div>
        <!-- /.form-box -->
    </div>
</template>

<script>
import {Form} from 'vform'

export default {
    name: "NovaSenha",
    created() {
        this.form.uuid = this.$route.params.id;
    },
    data() {
        return {
            formSubmit: false,
            form: new Form({
                password: '',
                confirm_password: '',
                uuid: ''
            })
        }
    },
    methods: {
        cadastro() {
            if (this.form.password === this.form.confirm_password) {
                this.form.put('/api/recuperacao-senha/' + this.form.uuid).then(({data}) => {
                    this.formSubmit = true;
                    this.$snotify.success(data.msg, 'Sucesso!');
                    this.$router.push('/');
                }).catch(error => {
                    if (error.hasOwnProperty('response') && error.response.status === 404) {
                        this.$swal.fire('Ooops!', 'Usuário não encontrado.', 'warning');
                    } else {
                        this.$swal.fire('Ooops!', 'Sistema instável no momento, tente mais tarde!', 'error');
                    }
                });
            } else {
                this.$swal.fire('Ooops!', 'Os campos não conferem.', 'warning');
            }
        }
    },
}
</script>

<style scoped>

</style>
