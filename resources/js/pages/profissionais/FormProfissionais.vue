<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="mb-3 col-sm-10 d-flex">
                <div v-if="false">
                    <b-form-checkbox id="checkbox-1" v-model="form.descontar_taxas_rateio" :value="false"
                                     :unchecked-value="false">
                        Descontar taxas no rateio
                    </b-form-checkbox>
                </div>
            </div>
            <div class="col-sm-4 mb-3">
                <b-form-checkbox v-model="form.possui_agenda" name="possui_agenda" switch>
                    <span v-if="form.possui_agenda">Possui Agenda</span>
                    <span v-else>Não Possui Agenda</span>
                </b-form-checkbox>
            </div>
            <div class="col-sm-8 mb-3">
                <b-form-checkbox v-model="form.status" name="check-button" switch>
                    <span v-if="form.status">Ativo</span>
                    <span v-else>Inativo</span>
                </b-form-checkbox>
            </div>
            <div class="form-group col-sm-5">
                <label>Nome Completo <b class="text-danger">*</b></label>
                <input type="text" class="form-control" v-model="form.individuo.nome" required>
            </div>
            <div class="form-group col-sm-4">
                <label>Apelido</label>
                <input type="text" class="form-control" v-model="form.individuo.apelido">
            </div>
            <div class="form-group col-sm-3">
                <label>Sexo</label>
                <select v-model="form.individuo.sexo" class="form-control">
                    <option value="">-Selecione-</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label>Data Nascimento</label>
                <input type="date" class="form-control" v-model="form.individuo.data_nascimento">
            </div>
            <div class="form-group col-sm-6">
                <label>CPF</label>
                <input type="text" class="form-control" v-model="form.individuo.documento" v-mask="'###.###.###-##'">
            </div>
            <div class="form-group col-sm-6">
                <label>Celular</label>
                <input type="text" class="form-control" v-model="form.individuo.celular" v-mask="'(##) #####-####'">
            </div>
        </div>
        <hr>
        <b-button v-b-toggle.collapse-1 variant="outline-primary">
            <i class="fas fa-user-lock"></i> Acesso ao Sistema <i class="fas fa-caret-down"></i>
        </b-button>
        <b-collapse id="collapse-1" class="mt-2">
            <b-card>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>Email</label>
                        <input type="email" class="form-control" v-model="usuario.email">
                    </div>
                    <div class="form-group col-sm-6" v-if="!possui_acesso">
                        <label>Senha</label>
                        <input type="password" class="form-control" v-model="usuario.password">
                    </div>
                    <div class="col-sm-12">
                        <h5><i class="fas fa-lock-open"></i> Controle de Acesso</h5>
                        <hr>
                        <b-form-checkbox-group
                            v-model="acessos"
                            :options="checkOptions"
                            class="mb-3"
                            value-field="item"
                            text-field="name"
                            disabled-field="notEnabled"
                        ></b-form-checkbox-group>
                    </div>
                </div>
            </b-card>
        </b-collapse>
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
    name: "FormProfissionais",
    props: {
        profissional: {
            required: true
        },
        controleAcesso: {
            required: true
        }
    },
    created() {
        this.acessos = this.setAcessos();
    },
    data() {
        return {
            load: false,
            form: {...this.profissional},
            possui_acesso: !!this.profissional.usuario?.email,
            usuario: {email: this.profissional.usuario?.email, password: '', id: this.profissional.usuario?.id},
            acessos: []
        }
    },
    computed: {
        checkOptions() {
            const options = [];
            this.controleAcesso.forEach(item => {
                options.push({item: item.id, name: item.nome});
            });

            return options;
        }
    },
    methods: {
        setAcessos() {
            const data = [];
            if (this.possui_acesso) {
                this.profissional.usuario.controle_acessos.forEach(acesso => {
                    data.push(acesso.id);
                });
                return data;
            }
            return []
        },
        onSubmit() {
            this.load = true;
            const formData = {...this.form, usuario: {...this.usuario, acessos: this.acessos}};
            if (!this.form.hasOwnProperty('id')) {
                axios.post('/api/profissionais', formData).then(_ => {
                    this.$snotify.success('Profissional cadastrado.', 'Sucesso');
                    this.$emit('loadData');
                    this.$bvModal.hide('modal-cadastro');
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            } else {
                axios.put('/api/profissionais/' + this.form.id, formData).then(_ => {
                    this.$snotify.success('Profissional alterado.', 'Sucesso');
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
