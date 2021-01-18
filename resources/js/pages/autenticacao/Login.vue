<template>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Bem vindo!</p>

            <form @submit.prevent="onSubmit">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" v-model="form.email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" v-model="form.password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7">
                        <p class="mt-2">
                            <router-link :to="{name: 'recuperar-senha'}" tag="a">
                                Esqueceu a senha?
                            </router-link>
                        </p>
                    </div>
                    <!-- /.col -->
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block" :disabled="load">
                            <span v-if="load">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                            <span v-else>Entrar <i class="fas fa-sign-in-alt"></i></span>
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>


            <hr>
            <div class="row">
                <div class="col">

                    <router-link :to="{name: 'registro'}" class="btn btn-outline-success btn-block">
                        Ainda não Possui? Teste grátis agora!
                    </router-link>

                </div>
            </div>
        </div>
        <!-- /.form-box -->
    </div>
</template>

<script>
import {mapActions} from 'vuex'

export default {
    name: "Login",
    data() {
        return {
            load: false,
            form: {
                email: '',
                password: ''
            }
        }
    },
    methods: {
        ...mapActions(['login']),
        async onSubmit() {
            this.load = true;
            const data = await this.login(this.form);
            if (data) {
                this.$snotify.success('Seja bem vindo!', 'Sucesso');
                if (data.countHorarioAtendimentos && data.countMeiosPagamentos &&
                    data.countProfissionaisServicos && data.countServicos) {
                    window.location.href = '/agenda';
                } else {
                    window.location.href = '/tour';
                }
            } else {
                this.load = false;
                this.$snotify.warning('Usuário ou senha inválidos!', 'Atenção')
            }
        }
    }
}
</script>

<style scoped>

</style>
