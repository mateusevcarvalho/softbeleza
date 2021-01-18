<template>
    <page-component title="Minha Conta" :breadcrumb="breadcrumb">
        <div class="row">
            <div class="col-sm-12">
                <card-component :load="load">

                    <div class="row">
                        <div class="col-sm-3">
                            <img src="/images/user-icon.png" width="70%" class="img-thumbnail">
                        </div>
                        <div class="col-sm-9">
                            <form @submit.prevent="onSubmit">
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <b-form-checkbox v-model="formData.alterarSenha" :unchecked-value="false">
                                            Alterar Senha
                                        </b-form-checkbox>
                                    </div>

                                    <div class="col-sm-6 form-group">
                                        <label>Nome<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" v-model="formData.nome" required>
                                    </div>

                                    <div class="col-sm-6 form-group">
                                        <label>Email<b class="text-danger">*</b></label>
                                        <input type="email" class="form-control" v-model="formData.email" disabled required>
                                    </div>

                                    <div class="col-sm-6 form-group">
                                        <label>Celular</label>
                                        <input type="text" class="form-control" v-model="formData.celular"
                                               v-mask="['(##) #####-####']">
                                    </div>

                                    <div class="col-sm-6 form-group" v-if="formData.alterarSenha">
                                        <label>Nova Senha<b class="text-danger">*</b></label>
                                        <input type="password" class="form-control" v-model="formData.password" required>
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Salvar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                </card-component>
            </div>
        </div>
    </page-component>
</template>

<script>
import axios from 'axios'


export default {
    name: "MeusDados",
    created() {
        const usuarioLogado = JSON.parse(localStorage.getItem('dataUsuario'));
        this.formData.nome = usuarioLogado.individuo.nome;
        this.formData.email = usuarioLogado.email;
        this.formData.celular = usuarioLogado.individuo.celular;
        axios.get('/api/usuario-logado').then(response => {
            const {usuario} = response.data;
            console.log(usuario)
            this.formData.nome = usuario.individuo.nome;
            this.formData.celular = usuario.individuo.celular;
        }).catch(error => {
            this.$store.dispatch('handleCatchError', error);
        });
    },
    data() {
        return {
            formData: {
                nome: '',
                email: '',
                celular: '',
                password: '',
                alterarSenha: false,
            },
            load: false,
            breadcrumb: [
                {name: 'Meus Dados', to: null},
            ],
        }
    },
    methods: {
        onSubmit() {
            this.load = true;
            axios.put('/api/meus-dados', this.formData).then(response => {
                this.$snotify.success('Dados alterados com sucesso', 'Sucesso');
                this.formData.password = '';
                this.formData.alterarSenha = false;
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
