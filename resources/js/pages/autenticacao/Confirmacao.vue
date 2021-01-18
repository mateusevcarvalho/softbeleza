<template>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Quase lá?</p>

            <form @submit.prevent="confirmar" @keydown="form.onKeydown($event)">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nome Completo" v-model="form.nome" required
                           :class="{ 'is-invalid': form.errors.has('nome') }" disabled>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="nome"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nome da empresa" required disabled
                           v-model="form.nome_empresa" :class="{ 'is-invalid': form.errors.has('nome_empresa') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-building"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="nome_empresa"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" v-model="form.email" required
                           :class="{ 'is-invalid': form.errors.has('email') }" disabled>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="email"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Senha" v-model="form.password" required
                           :class="{ 'is-invalid': form.errors.has('password') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-key"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="password"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Confirmação Senha"
                           v-model="form.password_confirmation"
                           required :class="{ 'is-invalid': form.errors.has('password_confirmation') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-key"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="password_confirmation"></has-error>
                </div>
                <div class="row">
                    <!-- /.col -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block" :disabled="form.busy">
                            <span v-if="form.busy">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                            <span v-else>Confirmar</span>
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
    import axios from 'axios'
    import {Form} from 'vform'
    import {mapActions} from 'vuex'

    export default {
        name: "Registro",
        async created() {
            const uuid = this.$route.params.id;
            this.form.uuid = uuid;
            await this.buscarEstabelecimento(uuid);
        },
        data() {
            return {
                locais: [],
                tiposEstabelecimentos: [],
                form: new Form({
                    nome: '',
                    nome_empresa: '',
                    email: '',
                    uuid: '',
                    password: '',
                    password_confirmation: '',
                })
            }
        },
        methods: {
            ...mapActions(['login', 'usuarioLogado']),
            async buscarEstabelecimento(uuid) {
                try {
                    const tenant = await axios.get('/api/buscar-tenant/' + uuid);
                    this.form.nome = tenant.data.nome;
                    this.form.email = tenant.data.email;
                    this.form.nome_empresa = tenant.data.nomeEmpresa;
                } catch (e) {
                    this.$snotify.error('Usuário já confirmado.', 'Oops!');
                    this.$router.push({name: 'Login'})
                }
            },

            async confirmar() {
                try {
                    const {data} = await this.form.post('/api/confirmar');
                    this.$snotify.success('Seja bem vindo.', 'Sucesso!');
                    const auth = await this.login({email: this.form.email, password: this.form.password});
                    if (auth) {
                        if (auth.countHorarioAtendimentos && auth.countMeiosPagamentos &&
                            auth.countProfissionaisServicos && auth.countServicos) {
                            window.location.href = '/agenda';
                        } else {
                            window.location.href = '/tour';
                        }
                    } else {
                        window.location.href = '/';
                    }
                } catch (e) {
                    this.$snotify.error('Falha interna, favor tente mais tarde.', 'Falha');
                }
            }
        }
    }
</script>

<style scoped>

</style>
