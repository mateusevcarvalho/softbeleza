<template>
    <page-component title="Rateio por Profissional" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="card">
                <form class="card-body" @submit.prevent="buscarDados">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>Agrupar por:</label>
                            <select class="form-control" v-model="filtro.agrupamento">
                                <option value="profissional">Profissional</option>
                                <option value="servico">Serviço</option>
                            </select>
                        </div>

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
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Profissional</th>
                        <th>Quantidade Total</th>
                        <th>Valor total</th>
                        <th>Valor proporcional de rateio</th>
                        <th v-if="filtro.agrupamento ==='profissional'">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="data in dataTable">
                        <td>{{filtro.agrupamento ==='servico' ? data.servico : data.profissional}}</td>
                        <td>{{data.total}}</td>
                        <td>{{data.valor_total | money}}</td>
                        <td>{{data.valor_rateio | money}}</td>
                        <td width="120" class="text-center" v-if="filtro.agrupamento ==='profissional'">
                            <router-link tag="span"
                                         :to="'/relatorios/rateio-profissional/detalhes/' + data.profissional_id"
                                         class="text-bold text-info mr-2" style="cursor: pointer">
                                <i class="fas fa-file-alt"></i> Detalhes
                            </router-link>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="table-secondary">
                        <td><b>Total:</b></td>
                        <td><b>{{dataTable.reduce((a, b) => a + b.total, 0)}}</b></td>
                        <td><b>{{dataTable.reduce((a, b) => a + b.valor_total, 0) | money}}</b></td>
                        <td colspan="2"><b>{{dataTable.reduce((a, b) => a + b.valor_rateio, 0) | money}}</b></td>
                    </tr>
                    </tfoot>
                </table>
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
            this.buscarDados();
        },
        data() {
            return {
                load: false,
                comandas: [],
                profissionais: [],
                servicos: [],
                filtro: {
                    data_inicio: '',
                    data_fim: '',
                    agrupamento: 'profissional',
                },
                breadcrumb: [
                    {name: 'Relatórios', to: null},
                    {name: 'Rateio por Profissional', to: null}
                ],
            }
        },
        computed: {
            dataTable() {
                const data = [];
                if (this.filtro.agrupamento === 'profissional') {
                    this.profissionais.forEach(profissional => {
                        const totalServicos = this.totalServicos(profissional);
                        data.push({
                            profissional: profissional.individuo.nome,
                            profissional_id: profissional.id,
                            total: totalServicos.quantidade_total,
                            valor_total: totalServicos.valor_total,
                            valor_rateio: totalServicos.valor_rateio
                        });
                    });
                } else if (this.filtro.agrupamento === 'servico') {
                    this.servicos.forEach(servico => {
                        const totalProfissionais = this.totalProfissionais(servico);
                        data.push({
                            servico: servico.nome,
                            total: totalProfissionais.quantidade_total,
                            valor_total: totalProfissionais.valor_total,
                            valor_rateio: totalProfissionais.valor_rateio
                        });
                    });
                }

                return data;
            }
        },
        methods: {
            ...mapActions(["buscarComandas", "buscarProfissionais", "buscarServicos"]),

            totalProfissionais(servico) {
                let contador = 0;
                let totalServicos = 0;
                let totalRateio = 0;
                this.comandas.forEach(comanda => {
                    comanda.comanda_itens.forEach(item => {
                        if (item.servico_id && item.servico_id === servico.id) {
                            contador += item.quantidade;
                            const valor = item.valor * item.quantidade;
                            const valorRateio = item.rateio ? (item.rateio * valor) / 100 : 0;
                            totalServicos += valor;
                            totalRateio += valorRateio;
                        }
                    });
                });

                return {quantidade_total: contador, valor_total: totalServicos, valor_rateio: totalRateio};
            },

            totalServicos(profissional) {
                let contador = 0;
                let totalServicos = 0;
                let totalRateio = 0;
                this.comandas.forEach(comanda => {
                    comanda.comanda_itens.forEach(item => {
                        if (item.servico_id && item.profissional_id === profissional.id) {
                            contador += item.quantidade;
                            const valor = item.valor * item.quantidade;
                            const valorRateio = item.rateio ? (item.rateio * valor) / 100 : 0;
                            totalServicos += valor;
                            totalRateio += valorRateio;
                        }
                    });
                });

                return {quantidade_total: contador, valor_total: totalServicos, valor_rateio: totalRateio};
            },

            buscarDados() {
                this.load = true;
                let filtro = 'with[]=comandaFormaPagamentos';
                filtro += '&with[]=comandaItens';
                filtro += `&where[tipo]=E`;
                filtro += `&where[estornada]=0`;
                filtro += `&where[status]=F`;
                filtro += `&whereBetween[data]=${this.filtro.data_inicio} 00:00:00,${this.filtro.data_fim} 23:59:59`;

                axios.all([
                    this.buscarComandas(filtro),
                    this.buscarProfissionais('with[]=individuo&where[status]=A'),
                    this.buscarServicos()
                ]).then(axios.spread((comandasResponse, profissionaisResponse, servicosResponse) => {
                    this.servicos = servicosResponse.data;
                    this.comandas = comandasResponse.data;
                    this.profissionais = profissionaisResponse.data;
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
