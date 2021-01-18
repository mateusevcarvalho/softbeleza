<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col">

                <div class="row">
                    <div class="form-group col-sm-12 mb-4">
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                <label>Desconto Geral</label>
                                <div class="row">
                                    <div class="col-sm-12 d-flex">
                                        <money class="form-control form-control-lg"
                                               v-model="formComanda.valor_desconto_total"
                                               v-bind="desconto"></money>
                                        <select class="form-control form-control-lg"
                                                v-model="formComanda.tipo_desconto_geral" style="width: 95px">
                                            <option value="D">R$</option>
                                            <option value="P">%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label>Forma de pagamento</label>
                        <v-select :options="meiosPagamentos"
                                  placeholder="-Selecione-"
                                  :reduce="meio => meio.id"
                                  @input="onFormaPagamento"
                                  label="nome"
                                  v-model="form.meios_pagamento_id">
                            <template #search="{attributes, events}">
                                <input class="vs__search" v-bind="attributes"
                                       v-on="events" style="height: calc(1.5em + 1rem + -4px) !important"/>
                            </template>
                            <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                        </v-select>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Valor</label>
                        <money v-model="form.valor" @keypress.enter="onConfirmar"
                               class="form-control form-control-lg"/>
                    </div>
                    <div class="col-sm-12 mb-3" v-if="meiosPagamento.hasOwnProperty('tipo_pagamento') &&
                            meiosPagamento.tipo_pagamento === 'P'">
                        <label>Parcelas</label> <br>
                        <button class="btn mr-2" type="button" @click.prevent="setParcelas(index)"
                                :class="{'btn-outline-secondary': !form.parcelas || (form.parcelas && form.parcelas != index),
                                    'btn-secondary':  form.parcelas && form.parcelas == index}"
                                v-for="index in parseInt(meiosPagamento.max_parcelas)">
                            {{index}}x
                        </button>
                    </div>
                </div>

                <b-button variant="dark" :disabled="totalAPagar === 0 || !form.meios_pagamento_id"
                          @click.prevent="onConfirmar">
                    <i class="fas fa-plus"></i> Adicionar
                </b-button>

                <table class="table table-striped table-hover mt-3" v-if="comanda_forma_pagamentos.length">
                    <thead>
                    <tr>
                        <th>Forma de Pagamento</th>
                        <th width="30%" class="text-center">Valor</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(formaPagamento, index) in comanda_forma_pagamentos">
                        <td>
                            {{formaPagamento.nome_meio_pagamento}}
                            <span v-if="formaPagamento.parcelas">({{formaPagamento.parcelas}}x)</span>
                        </td>
                        <td class="text-right">
                            {{formaPagamento.valor | money}}
                            <span class="text-danger ml-2" style="cursor: pointer"
                                  @click.prevent="onRemoverPagamentos(index)">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="bg-secondary p-3">
                    <b>SUBTOTAL:</b> <span class="float-right"><b>{{comanda.valor_total | money}}</b></span>
                </div>
            </div>
            <div class="col-sm-6 mt-3">
                <div class="bg-light p-3">
                    <b>Troco:</b>
                    <span class="float-right">
                        <b>{{troco | money}}</b>
                    </span>
                </div>
            </div>
            <div class="col-sm-6 mt-3">
                <div class="bg-light p-3 text-danger">
                    <b>Total á pagar:</b>
                    <span class="float-right">
                        <b>{{totalAPagar | money}}</b>
                    </span>
                </div>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-finalizar-operacao')">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" :disabled="load || totalAPagar !== 0">
                    Finalizar
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
    import moment from 'moment'
    import axios from 'axios'

    export default {
        name: "FinalizarOperacao",
        props: ['meiosPagamentos', 'comanda'],
        created() {
            this.form.valor = this.comanda.valor_total;
        },
        data() {
            return {
                load: false,
                comanda_forma_pagamentos: [],
                formComanda: {
                    valor_desconto_total: 0,
                    tipo_desconto_geral: 'D'
                },
                form: {
                    comanda_id: '',
                    meios_pagamento_id: '',
                    nome_meio_pagamento: null,
                    valor: '',
                    parcelas: '',
                },
                desconto: {
                    decimal: ',',
                    thousands: '.',
                    prefix: '',
                    suffix: '',
                    precision: 2,
                    masked: false
                }
            }
        },
        watch: {
            'formComanda.valor_desconto_total': function () {
                const valor = (this.comanda.valor_total - this.totalPagamentos) - this.totalDesconto;
                this.form.valor = valor < 0 ? 0 : valor;
            },

            'formComanda.tipo_desconto_geral': function () {
                const valor = (this.comanda.valor_total - this.totalPagamentos) - this.totalDesconto;
                this.form.valor = valor < 0 ? 0 : valor;
            }
        },
        computed: {
            meiosPagamento() {
                const meios = this.meiosPagamentos.find(meio => meio.id === this.form.meios_pagamento_id);
                if (meios)
                    return meios;
                return {}
            },

            totalPagamentos() {
                return this.comanda_forma_pagamentos.reduce((a, b) => a + b.valor, 0);
            },

            totalDesconto() {
                if (this.formComanda.tipo_desconto_geral === 'D') {
                    return this.formComanda.valor_desconto_total;
                } else {
                    return (this.formComanda.valor_desconto_total / 100) * parseInt(this.comanda.valor_total);
                }
            },

            troco() {
                const total = (this.totalPagamentos - this.comanda.valor_total) - this.totalDesconto;
                return total < 0 ? 0 : total;
            },

            totalAPagar() {
                const total = (this.comanda.valor_total - this.totalPagamentos) - this.totalDesconto;
                return total < 0 ? 0 : total;
            }
        },
        methods: {
            onConfirmar() {
                if (this.form.valor) {
                    if (this.meiosPagamento.forma_pagamento === 'C' && this.form.valor > this.totalAPagar) {
                        this.$snotify.warning('Valor informado é maior que o valor total da comanda.', 'Alerta');
                    } else {
                        const valorAdicionado = this.comanda_forma_pagamentos.reduce((a, b) => b.valor + a, 0);
                        this.comanda_forma_pagamentos.push({
                            ...this.form,
                            taxa: this.meiosPagamento.taxa_operadora,
                            nome_meio_pagamento: this.meiosPagamento.nome,
                            forma_pagamento: this.meiosPagamento.forma_pagamento
                        });
                        const valor = (this.comanda.valor_total - valorAdicionado) - this.form.valor;
                        this.form = {
                            comanda_id: '',
                            meios_pagamento_id: null,
                            valor: valor < 0 ? 0 : valor - this.totalDesconto,
                            parcelas: '',
                            nome_meio_pagamento: ''
                        }
                    }
                }
            },

            onFormaPagamento() {
                if (this.meiosPagamento.hasOwnProperty('tipo_pagamento') &&
                    this.meiosPagamento.tipo_pagamento === 'P') {
                    this.form.parcelas = '1';
                } else {
                    this.form.parcelas = '';
                }
            },

            onSubmit() {
                const formData = {
                    ...this.comanda,
                    ...this.formComanda,
                    valor_pago: this.totalPagamentos - this.troco,
                    data: moment().format('YYYY-MM-DD HH:mm:ss'),
                    status: 'F',
                    comanda_forma_pagamentos: this.comanda_forma_pagamentos
                };

                if (this.troco) {
                    formData.comanda_forma_pagamentos.forEach(forma => {
                        if (forma.forma_pagamento === 'D') {
                            forma.valor = forma.valor - this.troco;
                            return;
                        }
                    });
                }

                if (!this.totalAPagar) {
                    if (formData.hasOwnProperty('id')) {
                        this.load = true;
                        axios.put('/api/comandas/' + formData.id, formData).then(_ => {
                            this.$snotify.success('Operação finalizada.', 'Sucesso');
                            this.$emit('operacaoFinalizada');
                            this.$bvModal.hide('modal-finalizar-operacao');
                            this.load = false;
                        }).catch(error => {
                            this.$store.dispatch('handleCatchError', error);
                            this.load = false;
                        });
                    } else {
                        this.load = true;
                        axios.post('/api/comandas/', formData).then(_ => {
                            this.$snotify.success('Operação finalizada.', 'Sucesso');
                            this.$emit('operacaoFinalizada');
                            this.$bvModal.hide('modal-finalizar-operacao');
                            this.load = false;
                        }).catch(error => {
                            this.$store.dispatch('handleCatchError', error);
                            this.load = false;
                        });
                    }
                } else {
                    this.$snotify.warning('Ainda falta valores á pagar.', 'Alerta');
                }
            },

            onRemoverPagamentos(index) {
                this.comanda_forma_pagamentos.splice(index, 1);
                const valor = (this.comanda.valor_total - this.totalPagamentos) - this.form.valor;
                this.form.valor = valor - this.totalDesconto;
            },

            setParcelas(parcelas) {
                this.form.parcelas = parcelas;
            },
        }
    }
</script>

<style scoped>

</style>
