<template>
    <form @submit.prevent="onSubmit">
        <b-form-checkbox
                id="checkbox-1"
                v-model="novo_cliente"
                :value="true"
                :unchecked-value="false">
            Novo cliente.
        </b-form-checkbox>

        <div class="row mt-3" v-if="!novo_cliente">
            <div class="col-sm-12 form-group">
                <label>Cliente</label>
                <v-select :options="clientes"
                          placeholder="-Selecione-"
                          :reduce="cliente => cliente.id"
                          :getOptionLabel="cliente => cliente.individuo.nome"
                          v-model="cliente_id">
                    <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                </v-select>
            </div>
        </div>

        <div class="row mt-3" v-else>
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
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-clientes')">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" :disabled="load">
                    Incluir
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
    import lodash from "lodash";
    import {mapActions} from 'vuex';
    import axios from 'axios';

    export default {
        name: "FormClientes",
        props: ['clientes'],
        data() {
            return {
                load: false,
                cliente_id: '',
                novo_cliente: false,
                form: {
                    nota: '',
                    individuo: {
                        nome: '',
                        celular: '',
                        email: '',
                        sexo: '',
                        documento: '',
                        data_nascimento: '',
                    }
                },
            }
        },
        computed: {
            clienteSelecionado() {
                const cliente = this.clientes.find(cliente => cliente.id === this.cliente_id);
                return cliente;
            }
        },
        methods: {
            ...mapActions(['buscarClientes']),

            onSubmit() {
                if (this.novo_cliente) {
                    this.load = true;
                    axios.post('/api/clientes', this.form).then(response => {
                        const {data} = response;
                        const clienteId = data.cliente_id;
                        this.$emit('clienteId', {...this.form, id: clienteId});
                        this.$bvModal.hide('modal-clientes');
                        this.load = false;
                    }).catch(error => {
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                } else {
                    this.$emit('clienteId', this.clienteSelecionado);
                    this.$bvModal.hide('modal-clientes');
                }
            },
        }
    }
</script>

<style scoped>

</style>
