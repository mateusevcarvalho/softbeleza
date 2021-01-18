<template>
    <page-component :title="title" :breadcrumb="breadcrumb">
        <div class="row">
            <div class="col-sm-3">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{saldoInicial | money}}</h3>
                        <p>Saldo Inicial</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{entradas | money}}</h3>
                        <p>Total Entradas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{saidas | money}}</h3>
                        <p>Total Saídas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{subtotal | money}}</h3>
                        <p>Subtotal</p>
                    </div>
                    <div class="icon">
                        <i class="icofont-coins"></i>
                    </div>
                </div>
            </div>
        </div>
        <card-component :load="load">
            <div class="row">
                <div class="col">
                    <h5>Detalhamento do caixa</h5>
                </div>
                <div class="col text-right">
                    <button type="button" class="btn btn-dark" @click.prevent="reabrirCaixa"
                            :disabled="caixa && caixa.status === 'A'">
                        <i class="fas fa-cash-register"></i> Reabrir Caixa
                    </button>
                </div>
            </div>
            <hr>

            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Entradas</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <strong><i class="fas fa-hand-holding-usd"></i> Reforços</strong>
                    <b class="float-right">{{reforcos | money}}</b>
                    <hr>
                    <strong><i class="fas fa-store"></i> Vendas</strong>
                    <b class="float-right">{{vendas | money}}</b>
                    <ul>
                        <li v-for="formaPagamento in valoresFormasPagamentos">
                            {{formaPagamento.nome}} <span class="float-right">{{formaPagamento.valor | money}}</span>
                        </li>
                    </ul>
                </div>
                <!-- /.card-body -->
            </div>

            <div class="card card-danger mb-3">
                <div class="card-header">
                    <h3 class="card-title">Saídas</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <strong><i class="fas fa-minus-circle"></i> Saques</strong>
                    <b class="float-right">{{saques | money}}</b>
                    <hr>

                    <strong><i class="fas fa-sync-alt"></i> Estornos</strong>
                    <b class="float-right">{{estornos | money}}</b>
                </div>
                <!-- /.card-body -->
            </div>

            <div class="mt-4">
                <b-table
                        responsive
                        hover
                        small
                        :fields="[{label: '', key: 'comanda'}]"
                        :items="comandas"
                        :current-page="table.currentPage"
                        :per-page="table.perPage">
                    <template v-slot:cell(comanda)="comanda">
                        <span v-if="comanda.item.tipo === 'E'">
                            <span class="text-info"><i class="fas fa-piggy-bank"></i></span>
                            {{comanda.item.data | dateTimeBr}} - Recibo <b>{{comanda.item.valor_pago | money}}</b> -
                            Cliente: {{comanda.item.cliente ? comanda.item.cliente.individuo.nome : 'Não informado'}} /
                            <span class="text-info" style="cursor: pointer"
                                  @click.prevent="abrirModalComanda(comanda.item)">
                                #{{comanda.item.id}}
                            </span>
                        </span>
                        <span v-if="comanda.item.tipo === 'S'">
                            <span class="text-danger"><i class="icofont-database-remove"></i></span>
                            {{comanda.item.data | dateTimeBr}} - Saque <b>{{comanda.item.valor_pago | money}}</b>
                        </span>
                        <span v-if="comanda.item.tipo === 'ES'">
                            <span class="text-danger"><i class="fas fa-sync-alt"></i></span>
                            {{comanda.item.data | dateTimeBr}} - Estorno <b>{{comanda.item.valor_pago | money}}</b> -
                            Cliente: {{comanda.item.cliente ? comanda.item.cliente.individuo.nome : 'Não informado'}}
                        </span>
                        <span v-if="comanda.item.tipo === 'R'">
                            <span class="text-success"><i class="icofont-money"></i></span>
                            {{comanda.item.data | dateTimeBr}} - Reforço <b>{{comanda.item.valor_pago | money}}</b>
                        </span>
                    </template>
                    <template v-slot:head(comanda)="data">
                        <h5>Transações</h5>
                    </template>
                </b-table>

                <b-pagination
                        v-if="comandas.length > table.perPage"
                        v-model="table.currentPage"
                        :total-rows="comandas.length"
                        :per-page="table.perPage"
                        class="mt-3 text-center"/>
            </div>

            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <router-link :to="{name: 'fechamento-caixa'}" class="btn btn-dark">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </router-link>
                </div>
            </div>
        </card-component>

        <b-modal size="lg" id="modal-comanda" hide-footer :title="modalTitle">
            <modal-comanda @loadDados="handleLoadDados" :comanda="modalComanda"/>
        </b-modal>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'
    import Card from "../../../components/Card";
    import ModalComanda from "./ModalComanda";
    import axios from 'axios'

    export default {
        name: "Detalhes",
        components: {ModalComanda, Card},
        created() {
            this.buscarDados();
        },
        data() {
            return {
                load: false,
                title: 'Caixa:',
                caixa: null,
                modalComanda: null,
                table: {
                    currentPage: 1,
                    perPage: 15,
                }
            }
        },
        methods: {
            ...mapActions(["buscarCaixas"]),

            reabrirCaixa() {
                this.load = true;
                axios.put('/api/reabrir-caixa/' + this.caixa.id).then(_ => {
                    this.$snotify.success('Caixa reaberto.', 'Sucesso');
                    this.load = false;
                    this.buscarDados();
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.$snotify.warning('Usuário responsável pelo caixa, não deve possui outro aberto.', 'Alerta');
                    }
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            abrirModalComanda(comanda) {
                this.modalComanda = {...comanda};
                this.$bvModal.show('modal-comanda')
            },

            handleLoadDados() {
                this.buscarDados();
            },

            buscarDados() {
                let url = 'with[]=usuario.individuo';
                url += '&with[]=comandas.comandaFormaPagamentos.meiosPagamento';
                url += '&with[]=comandas.cliente.individuo';
                url += '&with[]=comandas.comandaItens.produto';
                url += '&with[]=comandas.comandaItens.servico';

                this.load = true;
                this.buscarCaixas(`${url}&where[id]=${this.$route.params.id}&first=true`).then(response => {
                    this.caixa = response.data;
                    this.title = 'Caixa: ' + this.caixa.usuario.individuo.nome + ' - ' +
                        this.$options.filters.dateBr(this.caixa.data_abertura);
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            }
        },
        computed: {
            modalTitle() {
                if (this.modalComanda) {
                    return 'Detalhes da comanda: #' + this.modalComanda.id;
                }

                return '';
            },

            saldoInicial() {
                if (this.caixa)
                    return this.caixa.saldo_inicial;
                return 0;
            },

            comandas() {
                if (this.caixa) {
                    return this.caixa.comandas.filter(comanda => comanda.status === 'F');
                }

                return [];
            },

            valoresFormasPagamentos() {
                if (this.caixa) {
                    let valores = [];
                    this.caixa.comandas.filter(comanda => comanda.tipo === 'E').forEach(comanda => {
                        comanda.comanda_forma_pagamentos.forEach(formaPagamento => {
                            valores.push({
                                nome: formaPagamento.meios_pagamento.nome,
                                valor: formaPagamento.valor,
                                key: formaPagamento.meios_pagamento_id
                            });
                        });
                    });

                    const formasPagamentos = [...new Set(valores.map(forma => forma.nome))];
                    const valoresFormasPagamentos = [];
                    formasPagamentos.forEach(formaPagamento => {
                        valoresFormasPagamentos.push({
                            nome: formaPagamento,
                            valor: valores.filter(valor => valor.nome === formaPagamento).reduce((a, b) => a + b.valor, 0)
                        });
                    });

                    return valoresFormasPagamentos;
                }

                return [];
            },

            entradas() {
                if (this.caixa)
                    return this.caixa.comandas
                        .filter(comanda => (comanda.tipo === 'E' || comanda.tipo === 'R'))
                        .reduce((a, b) => a + b.valor_pago, 0);
                return 0;
            },

            saidas() {
                if (this.caixa)
                    return this.caixa.comandas
                        .filter(comanda => comanda.tipo === 'S' || (comanda.estornada && comanda.tipo !== 'ES'))
                        .reduce((a, b) => a + b.valor_pago, 0);
                return 0;
            },

            saques() {
                if (this.caixa)
                    return this.caixa.comandas
                        .filter(comanda => comanda.tipo === 'S')
                        .reduce((a, b) => a + b.valor_pago, 0);
                return 0;
            },

            estornos() {
                if (this.caixa)
                    return this.caixa.comandas
                        .filter(comanda => comanda.tipo === 'ES')
                        .reduce((a, b) => a + b.valor_pago, 0);
                return 0;
            },

            reforcos() {
                if (this.caixa)
                    return this.caixa.comandas
                        .filter(comanda => comanda.tipo === 'R')
                        .reduce((a, b) => a + b.valor_pago, 0);
                return 0;
            },

            vendas() {
                if (this.caixa)
                    return this.caixa.comandas
                        .filter(comanda => comanda.tipo === 'E')
                        .reduce((a, b) => a + b.valor_pago, 0);
                return 0;
            },

            subtotal() {
                return (this.saldoInicial + this.entradas) - this.saidas;
            },

            breadcrumb() {
                return [
                    {name: 'Relatórios', to: null},
                    {name: 'Fechamento de Caixa', to: 'fechamento-caixa'},
                    {name: this.title, to: null}
                ];
            }
        }
    }
</script>

<style scoped>

</style>
