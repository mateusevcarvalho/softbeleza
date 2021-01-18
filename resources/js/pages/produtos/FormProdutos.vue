<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col-sm-6 form-group">
                <label>Nome<b class="text-danger">*</b></label>
                <input type="text" class="form-control" v-model="form.nome" required>
            </div>

            <div class="col-sm-6 form-group">
                <label>Valor de venda</label>
                <money class="form-control" v-model="form.valor"></money>
            </div>

            <div class="col-sm-6 form-group">
                <label>Desconto Máximo</label>
                <money class="form-control" v-model="form.desconto_maximo" v-bind="percent"></money>
            </div>

            <div class="col-sm-6 form-group">
                <label>Desconto Promocional</label>
                <money class="form-control" v-model="form.desconto_promocional" v-bind="percent"></money>
            </div>

            <div class="form-group col-sm-12">
                <label>Descrição</label>
                <textarea class="form-control" v-model="form.descricao"></textarea>
            </div>
        </div>

        <h6 class="text-bold mt-3">Configuração de estoque</h6>
        <hr>
        <div class="row">
            <div class="form-group col-sm-6">
                <label>Unidade Medida de Entrada <b class="text-danger">*</b></label>
                <select v-model="form.unidade_medida_entrada" class="form-control" required>
                    <option value="">-Selecione-</option>
                    <option value="UN">Unidade</option>
                    <option value="L">Litro</option>
                    <option value="ML">ML</option>
                    <option value="CX">Caixa</option>
                </select>
            </div>

            <div class="col-sm-6 form-group">
                <label>Custo unitário
                    <span v-if="form.unidade_medida_entrada">por ({{form.unidade_medida_entrada}})</span>
                </label>
                <money class="form-control" v-model="form.valor_unitario"></money>
            </div>

            <div class="form-group col-sm-6">
                <label>Unidade Medida de Saída <b class="text-danger">*</b></label>
                <select v-model="form.unidade_medida_saida" class="form-control" required>
                    <option value="">-Selecione-</option>
                    <option value="UN">Unidade</option>
                    <option value="L">Litro</option>
                    <option value="ML">ML</option>
                    <option value="CX">Caixa</option>
                </select>
            </div>

            <div class="col-sm-6 form-group">
                <label>Custo fracionado
                    <span v-if="form.unidade_medida_saida">por ({{form.unidade_medida_saida}})</span>
                </label>
                <money class="form-control" v-model="form.valor_fracionado"></money>
            </div>

            <div class="col-sm-6 form-group" v-if="form.unidade_medida_saida && form.unidade_medida_entrada">
                <label>Equivalência
                    <span>{{form.unidade_medida_entrada}}/{{form.unidade_medida_saida}}</span>
                    <b class="text-danger">*</b>
                </label>
                <input type="number" class="form-control" v-model="form.equivalencia" required>
            </div>
            <div class="col-sm-6 mt-5" v-if="form.unidade_medida_saida && form.unidade_medida_entrada">
                1 {{form.unidade_medida_entrada}} equivale a <span class="text-info">{{form.equivalencia}} {{form.unidade_medida_saida}}</span>
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
    </form>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "FormProdutos",
        props: {
            produto: {
                required: true
            }
        },
        data() {
            return {
                load: false,
                form: {...this.produto},
                percent: {
                    decimal: ',',
                    thousands: '.',
                    prefix: '',
                    suffix: ' %',
                    precision: 2,
                    masked: false
                }
            }
        },

        methods: {
            onSubmit() {
                this.load = true;
                if (this.form.id) {
                    axios.put(`/api/produtos/${this.form.id}`, this.form).then(_ => {
                        this.$snotify.success('Produto alterado.', 'Sucesso');
                        this.$emit('loadData');
                        this.$bvModal.hide('modal-cadastro');
                        this.load = false;
                    }).catch(error => {
                        this.$store.dispatch('handleCatchError', error);
                        this.load = false;
                    });
                } else {
                    axios.post('/api/produtos', this.form).then(_ => {
                        this.$snotify.success('Produto cadastrado.', 'Sucesso');
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
