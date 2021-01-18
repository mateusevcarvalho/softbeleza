<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="mb-3 col-sm-10 d-flex">

            </div>
            <div class="col-sm-2 text-right">
                <b-form-checkbox v-model="form.status" name="check-button" switch>
                    <span v-if="form.status">Ativo</span>
                    <span v-else>Inativo</span>
                </b-form-checkbox>
            </div>
            <div class="form-group col-sm-4">
                <label>Nome Fantasia <b class="text-danger">*</b></label>
                <input type="text" class="form-control" v-model="form.individuo.nome" required>
            </div>
            <div class="form-group col-sm-4">
                <label>CNPJ</label>
                <input type="text" class="form-control" v-model="form.individuo.documento"
                       v-mask="'##.###.###/####-##'">
            </div>
            <div class="form-group col-sm-4">
                <label>Email</label>
                <input type="email" class="form-control" v-model="form.individuo.email_contato">
            </div>
            <div class="form-group col-sm-3">
                <label>Celular</label>
                <input type="text" class="form-control" v-model="form.individuo.celular" v-mask="'(##) #####-####'">
            </div>
            <div class="form-group col-sm-3">
                <label>Fixo</label>
                <input type="text" class="form-control" v-model="form.individuo.telefone_fixo"
                       v-mask="'(##) ####-####'">
            </div>
            <div class="form-group col-sm-3">
                <label>Nome Responsável</label>
                <input type="text" class="form-control" v-model="form.individuo.responsavel">
            </div>
            <div class="form-group col-sm-3">
                <label>Contato Responsável</label>
                <input type="text" class="form-control" v-model="form.individuo.contato_responsavel"
                       v-mask="['(##) ####-####', '(##) #####-####']">
            </div>
        </div>

        <h6 class="text-bold mt-3">Endereço</h6>
        <hr>
        <div class="row">
            <div class="col-sm-2 form-group">
                <label>Cep </label>
                <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.cep"
                       v-mask="'#####-###'" @change="cepOnChange">
            </div>

            <div class="col-sm-4 form-group">
                <label>Logradouro</label>
                <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.logradouro">
            </div>

            <div class="col-sm-4 form-group">
                <label>Bairro</label>
                <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.bairro">
            </div>

            <div class="col-sm-2 form-group">
                <label>Número</label>
                <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.numero"
                       ref="numero">
            </div>

            <div class="col-sm-4 form-group">
                <label>Estado</label>
                <v-select :options="estados"
                          placeholder="-Selecione-"
                          @input="estadoOnChange"
                          :reduce="estado => estado.id" label="nome"
                          v-model="form.individuo.individuos_endereco.estado_id">
                    <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                </v-select>
            </div>

            <div class="col-sm-4 form-group">
                <label>Cidade</label>
                <v-select :options="cidades"
                          placeholder="-Selecione-"
                          :reduce="cidade => cidade.id" label="nome"
                          v-model="form.individuo.individuos_endereco.cidade_id">
                    <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                </v-select>
            </div>

            <div class="col-sm-4 form-group">
                <label>Complemento</label>
                <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.complemento">
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
        <div class="overlay d-flex justify-content-center align-items-center" v-if="load">
            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </form>
</template>

<script>
    import axios from 'axios'
    import {mapActions} from 'vuex'

    export default {
        name: "FormFornecedores",
        props: {
            fornecedor: {
                required: true
            },
            estados: {
                required: true
            }
        },
        async created() {
            if (this.fornecedor.hasOwnProperty('id')) {
                const estadoId = this.fornecedor.individuo.individuos_endereco.estado_id;
                if (estadoId) {
                    this.load = true;
                    const cidades = await this.buscaCidades(`where[estado_id]=${estadoId}`);
                    this.cidades = cidades.data;
                    this.load = false;
                }
            }
        },
        data() {
            return {
                load: false,
                cidades: [],
                form: {...this.fornecedor}
            }
        },
        methods: {
            ...mapActions(['buscaCidades', 'buscaEstados', 'buscaCep']),
            onSubmit() {
                this.load = true;
                if (!this.form.hasOwnProperty('id')) {
                    axios.post('/api/fornecedores', this.form).then(_ => {
                        this.$snotify.success('Fornecedor cadastrado.', 'Sucesso');
                        this.$emit('loadData');
                        this.$bvModal.hide('modal-cadastro');
                        this.load = false;
                    }).catch(error => {
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                } else {
                    axios.put('/api/fornecedores/' + this.form.id, this.form).then(_ => {
                        this.$snotify.success('Fornecedor alterado.', 'Sucesso');
                        this.$emit('loadData');
                        this.$bvModal.hide('modal-cadastro');
                        this.load = false;
                    }).catch(error => {
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                }
            },

            async cepOnChange() {
                try {
                    this.load= true;
                    const {data} = await this.buscaCep(this.form.individuo.individuos_endereco.cep);
                    this.form.individuo.individuos_endereco.logradouro = data.logradouro;
                    this.form.individuo.individuos_endereco.bairro = data.bairro;

                    const estado = await this.buscaEstados(`where[sigla]=${data.uf}&first=true`);
                    this.form.individuo.individuos_endereco.estado_id = estado.data.id;

                    const cidades = await this.buscaCidades(`where[estado_id]=${estado.data.id}`);
                    this.cidades = cidades.data;
                    this.form.individuo.individuos_endereco.cidade_id = cidades.data.find(cidade => cidade.ibge === data.ibge).id;

                    this.$refs.numero.focus();
                    this.load= false;
                } catch (e) {
                    this.$snotify.error('Não foi possível buscar este cep no momento.', 'Falha');
                    this.$emit('onLoad', false);
                }
            },

            async estadoOnChange() {
                this.load= true;
                const {data} = await this.buscaCidades(`where[estado_id]=${this.form.individuo.individuos_endereco.estado_id}`);
                this.cidades = data;
                this.load= false;
            }
        }
    }
</script>

<style scoped>

</style>
