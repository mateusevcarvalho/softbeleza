<template>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg" v-if="!formSubmit">Vamos lá?</p>

            <form @submit.prevent="cadastro" @keydown="form.onKeydown($event)" v-if="!formSubmit">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nome Completo" v-model="form.nome" required
                           :class="{ 'is-invalid': form.errors.has('nome') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="nome"></has-error>
                </div>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="form.tipos_estabelecimento_id"
                            :class="{ 'is-invalid': form.errors.has('tipos_estabelecimento_id') }" required>
                        <option value="">Tipo de Estabelecimento</option>
                        <option :value="tipo.id" v-for="tipo in tipos">
                            {{tipo.nome}}
                        </option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-cut"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="tipos_estabelecimento_id"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nome da empresa" required
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
                           :class="{ 'is-invalid': form.errors.has('email') }">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="email"></has-error>
                </div>
                <div class="input-group mb-3">
                    <select class="form-control" v-model="form.local_id" required
                            :class="{ 'is-invalid': form.errors.has('local_id') }">
                        <option value="">Onde nos encontrou</option>
                        <option :value="local.id" v-for="local in locais">
                            {{local.nome}}
                        </option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-info"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="local_id"></has-error>
                </div>
                <div class="row">
                    <!-- /.col -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block" :disabled="form.busy">
                            <span v-if="form.busy">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                            <span v-else>Cadastrar</span>
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class="alert alert-success text-center" v-else>
                Cadastro realizado com sucesso! <b>Enviamos um email de confirmação</b>.
            </div>

            <div class="mt-3 text-center" v-if="!formSubmit">
                <router-link :to="{name: 'login'}" class="text-center">
                    Já possui cadastrado? Clique aqui
                </router-link>
            </div>
        </div>
        <!-- /.form-box -->
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex'
    import {Form} from 'vform'

    export default {
        name: "Registro",
        async created() {
            try {
                const locais = await this.buscarLocais();
                const tipos = await this.buscarTiposEstabelecimento();
                this.locais = locais.data;
                this.tipos = tipos.data;
            } catch (e) {
                this.$snotify.error('Falha ao carregar dados', 'Erro');
            }
        },
        data() {
            return {
                formSubmit: false,
                locais: [],
                tipos: [],
                form: new Form({
                    nome: '',
                    tipos_estabelecimento_id: '',
                    nome_empresa: '',
                    email: '',
                    local_id: ''
                })
            }
        },
        methods: {
            ...mapActions(['buscarLocais', 'buscarTiposEstabelecimento']),
            cadastro() {
                this.form.post('/api/cadastro').then(({data}) => {
                    this.formSubmit = true;
                });
            }
        },
    }
</script>

<style scoped>

</style>
