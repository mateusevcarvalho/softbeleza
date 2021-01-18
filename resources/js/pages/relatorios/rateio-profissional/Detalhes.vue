<template>
    <page-component :title="title()" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="card">
                <form class="card-body" @submit.prevent="buscarDados">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>Data inicio</label>
                            <input type="date" class="form-control" v-model="filtro.data_inicio">
                        </div>

                        <div class="form-group col-sm-4">
                            <label>Data fim</label>
                            <input type="date" class="form-control" v-model="filtro.data_fim">
                        </div>
                    </div>
                    <hr>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Pesquisar</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm">
                    <thead>
                    <tr>
                        <th>Data</th>
                        <th>Comanda</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Qtd</th>
                        <th>Valor Serviço</th>
                        <th>Comissão</th>
                        <th>Vlr. Rateio</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="data in dataTable">
                        <td>{{data.data | dateTimeBr}}</td>
                        <td>{{data.comanda}}</td>
                        <td>{{data.cliente}}</td>
                        <td>{{data.servico}}</td>
                        <td>{{data.quantidade}}</td>
                        <td>{{data.valor | money}}</td>
                        <td>{{data.comissao | percent}}</td>
                        <td>{{data.valor_rateio | money}}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="table-secondary">
                        <td colspan="5"><b>Total:</b></td>
                        <td colspan="2"><b>{{dataTable.reduce((a, b) => a + b.valor, 0) | money}}</b></td>
                        <td><b>{{dataTable.reduce((a, b) => a + b.valor_rateio, 0) | money}}</b></td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <router-link :to="{name: 'rateio-profissional'}" class="btn btn-dark">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </router-link>
                </div>
            </div>
        </card-component>
    </page-component>
</template>

<script>
    import axios from 'axios'
    import {mapActions} from 'vuex'
    import moment from "moment";

    export default {
        name: "RateioProfissional",
        created() {
            this.filtro.data_inicio = moment().startOf('month').format('YYYY-MM-DD');
            this.filtro.data_fim = moment().format('YYYY-MM-DD');
            this.buscarDados(this.$route.params.id);
        },
        data() {
            return {
                load: false,
                comandas: [],
                nomeProfissional: '',
                filtro: {
                    data_inicio: '',
                    data_fim: '',
                },
                breadcrumb: [
                    {name: 'Relatórios', to: null},
                    {name: 'Rateio por Profissional', to: 'rateio-profissional'},
                ],
            }
        },
        computed: {
            dataTable() {
                const data = [];
                this.comandas.forEach(comanda => {
                    comanda.comanda_itens.forEach(item => {
                        const valor = item.valor * item.quantidade;
                        const valorRateio = (valor * item.rateio) / 100
                        if (item.servico_id && parseInt(item.profissional_id) === parseInt(this.$route.params.id)) {
                            data.push({
                                data: comanda.data,
                                comanda: '#' + comanda.id,
                                cliente: comanda.cliente.individuo.nome,
                                servico: item.servico.nome,
                                quantidade: item.quantidade,
                                valor: valor,
                                comissao: item.rateio,
                                valor_rateio: valorRateio
                            });
                        }
                    });
                });

                return data;
            }
        },
        methods: {
            ...mapActions(["buscarComandas", "buscarProfissionais"]),

            title() {
                return 'Rateio de ' + this.nomeProfissional;
            },

            buscarDados(profissionalId) {
                this.load = true;
                let filtro = 'with[]=comandaFormaPagamentos';
                filtro += '&with[]=comandaItens.servico';
                filtro += '&with[]=cliente.individuo';
                filtro += `&where[tipo]=E`;
                filtro += `&where[estornada]=0`;
                filtro += `&where[status]=F`;
                filtro += `&whereHas[comandaItens]=profissional_id,${profissionalId}`;
                filtro += `&whereBetween[data]=${this.filtro.data_inicio} 00:00:00,${this.filtro.data_fim} 23:59:59`;

                axios.all([
                    this.buscarComandas(filtro),
                    this.buscarProfissionais(`with[]=individuo&where[id]=${profissionalId}&first=true`),
                ]).then(axios.spread((comandasResponse, profissional) => {
                    this.comandas = comandasResponse.data;
                    this.nomeProfissional = profissional.data.individuo.nome;
                    this.breadcrumb.push({name: this.title(), to: null});
                    this.load = false;
                })).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>
