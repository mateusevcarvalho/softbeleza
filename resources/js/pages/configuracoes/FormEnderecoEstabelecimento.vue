<template>
    <form @submit.prevent="onSubmit">
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
            <div class="col text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    import {mapActions} from 'vuex';

    export default {
        name: "FormEnderecoEstabelecimento",
        props: {
            estados: {
                required: true,
                type: Array | Object
            },
            formEstabelecimento: {
                required: true
            }
        },
        data() {
            return {
                cidades: [],
                form: {...this.formEstabelecimento}
            }
        },
        watch: {
            async formEstabelecimento(newValue) {
                const estadoId = newValue.individuo.individuos_endereco.estado_id;
                if (estadoId) {
                    this.$emit('onLoad', true);
                    const cidades = await this.buscaCidades(`where[estado_id]=${estadoId}`);
                    this.cidades = cidades.data;
                    this.$emit('onLoad', false);
                }

                this.form = newValue;
            }
        },
        methods: {
            ...mapActions(['buscaCep', 'buscaEstados', 'buscaCidades']),
            onSubmit() {
                this.$emit('onLoad', true);
                axios.put('/api/estabelecimentos/' + this.form.id, this.form)
                    .then(_ => {
                        this.$snotify.success('Endereco salvo.', 'Sucesso');
                        this.$emit('onLoad', false);
                    }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.$emit('onLoad', false);
                })
            },

            async cepOnChange() {
                try {
                    this.$emit('onLoad', true);
                    const {data} = await this.buscaCep(this.form.individuo.individuos_endereco.cep);
                    this.form.individuo.individuos_endereco.logradouro = data.logradouro;
                    this.form.individuo.individuos_endereco.bairro = data.bairro;

                    const estado = await this.buscaEstados(`where[sigla]=${data.uf}&first=true`);
                    this.form.individuo.individuos_endereco.estado_id = estado.data.id;

                    const cidades = await this.buscaCidades(`where[estado_id]=${estado.data.id}`);
                    this.cidades = cidades.data;
                    this.form.individuo.individuos_endereco.cidade_id = cidades.data.find(cidade => cidade.ibge === data.ibge).id;

                    this.$refs.numero.focus();
                    this.$emit('onLoad', false);
                } catch (e) {
                    this.$snotify.error('Não foi possível buscar este cep no momento.', 'Falha');
                    this.$emit('onLoad', false);
                }
            },

            async estadoOnChange() {
                this.$emit('onLoad', true);
                const {data} = await this.buscaCidades(`where[estado_id]=${this.form.individuo.individuos_endereco.estado_id}`);
                this.cidades = data;
                this.$emit('onLoad', false);
            }
        }
    }
</script>

<style scoped>

</style>
