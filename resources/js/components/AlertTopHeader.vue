<template>
    <div class="alert-soft-success text-center main-header" style="padding: 10px" v-if="alertaAvaliacao">
        Bem-vindo(a)! Você ainda tem
        <span class="text-warning" style="font-size: larger">
            {{ diasRestantes }} {{ diasRestantes === 1 ? 'dia' : 'dias' }}
        </span>
        para testar o SoftBeleza gratuitamente. Após esse período, continue usando mediante pagamento.

        <span class="float-right mb-2" v-if="$auth.can('administrativo')"
              @click.prevent="vm.$bvModal.show('modal-pagamento')">
            <a href="javascript:void(0)" class="btn btn-sm btn-warning">Efetuar Pagamento</a>
        </span>

        <b-modal size="xl" id="modal-pagamento" hide-header hide-footer no-close-on-backdrop no-close-on-esc>
            <div class="text-center">
                <h5>
                    Continue atraindo clientes e organizando sua gestão por apenas:
                    <span class="text-success">R$ 49,90/mês</span>
                </h5>
                <p class="text-muted">Preencha os dados abaixo para realizar o pagamento.</p>
            </div>

            <form class="mt-4" @submit.prevent="onSubmit">
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label>CPF/CNPJ*</label>
                        <input type="text" class="form-control" v-mask="['###.###.###-##', '##.###.###-####-##']"
                               required v-model="form.individuo.documento">
                    </div>
                    <div class="col-sm-8 form-group">
                        <label>Nome Completo/Razão Social*</label>
                        <input type="text" class="form-control" required v-model="form.individuo.nome_razao_social">
                    </div>

                    <div class="col-sm-3 form-group">
                        <label>Cep </label>
                        <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.cep"
                               v-mask="'#####-###'" @change="cepOnChange">
                    </div>

                    <div class="col-sm-7 form-group">
                        <label>Logradouro</label>
                        <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.logradouro">
                    </div>

                    <div class="col-sm-2 form-group">
                        <label>Número</label>
                        <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.numero"
                               ref="numero">
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>Bairro</label>
                        <input type="text" class="form-control" v-model="form.individuo.individuos_endereco.bairro">
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
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-default" @click="$bvModal.hide('modal-pagamento')">
                            <i class="fas fa-times"></i> Fechar
                        </button>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="submit" class="btn btn-success" :disabled="load">
                            <i class="fas fa-save"></i> Realizar Pagamento
                        </button>
                    </div>
                </div>
            </form>

            <div class="overlay d-flex justify-content-center align-items-center bg-blue-light" v-if="load">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import moment from "moment";
import {mapActions} from "vuex";
import axios from 'axios'

const formInitial = {
    individuo: {
        documento: '',
        nome_razao_social: '',
        individuos_endereco: {
            cep: '',
            logradouro: '',
            bairro: '',
            cidade_id: '',
            estado_id: '',
            numero: ''
        }
    }
}

export default {
    name: "AlertTopHeader",
    async created() {
        this.verificarDataAvaliacao();
        const estados = await this.buscaEstados();
        this.estados = estados.data;
    },
    data() {
        return {
            load: false,
            vm: this,
            alertaAvaliacao: true,
            diasRestantes: 0,
            cidades: [],
            estados: [],
            form: formInitial
        }
    },
    methods: {
        ...mapActions(['buscaCep', 'buscaEstados', 'buscaCidades', 'handleCatchError']),

        async onSubmit() {
            this.load = true;
            try {
                const {data} = await axios.post('/api/cadastrar-pagamento', this.form);
                this.$snotify.success(data.msg);
                setTimeout(() => {
                    this.load = false;
                    this.$bvModal.hide('modal-pagamento');
                    window.open(window.Url + '/checkout/' + data.data.uuid, '_blank');
                }, 800);
            } catch (e) {
                this.load = false;
                await this.$store.dispatch('handleCatchError', e);
            }
        },

        verificarDataAvaliacao() {
            const usuario = JSON.parse(localStorage.getItem('dataUsuario'));
            const diasAvaliacao = usuario.tenant.dias_avaliacao;
            const dataCriacao = usuario.tenant.created_at;
            const diasRestantes = diasAvaliacao - moment().diff(moment(dataCriacao), 'days');
            this.diasRestantes = diasRestantes >= 1 ? diasRestantes : 0;
        },

        async cepOnChange() {
            try {
                this.load = true;
                const {data} = await this.buscaCep(this.form.individuo.individuos_endereco.cep);
                this.form.individuo.individuos_endereco.logradouro = data.logradouro;
                this.form.individuo.individuos_endereco.bairro = data.bairro;

                const estado = await this.buscaEstados(`where[sigla]=${data.uf}&first=true`);
                this.form.individuo.individuos_endereco.estado_id = estado.data.id;

                const cidades = await this.buscaCidades(`where[estado_id]=${estado.data.id}`);
                this.cidades = cidades.data;
                this.form.individuo.individuos_endereco.cidade_id = cidades.data.find(cidade => cidade.ibge === data.ibge).id;

                this.$refs.numero.focus();
                this.load = false;
            } catch (e) {
                this.load = false;
                this.$snotify.warning('Não foi possível buscar este cep no momento.', 'Falha');
            }
        },

        async estadoOnChange() {
            this.load = true;
            const {data} = await this.buscaCidades(`where[estado_id]=${this.form.individuo.individuos_endereco.estado_id}`);
            this.cidades = data;
            this.load = false;
        }
    }
}
</script>

<style scoped>

</style>
