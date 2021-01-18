<template>
    <page-component title="Faturamento por Forma de Pagamento" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>Agrupar por:</label>
                            <select class="form-control" v-model="filtro.agrupamento">
                                <option value="dia">Dia</option>
                                <option value="mes">Mês</option>
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
                </div>
            </div>


            <v-chart style="width: 100%" autoresize :options="option"/>
        </card-component>
    </page-component>
</template>

<script>
    import moment from "moment";
    import {mapActions} from "vuex";

    export default {
        name: "FaturamentoFormaPagamento",
        created() {
            this.filtro.data_inicio = moment().startOf('month').format('YYYY-MM-DD');
            this.filtro.data_fim = moment().format('YYYY-MM-DD');
            this.buscarDados();
            this.buscarMeiosPagamentos().then(response => {
                this.formasPagamento = response.data;
            }).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },
        data() {
            return {
                load: false,
                filtro: {
                    agrupamento: 'dia',
                    data_inicio: '',
                    data_fim: ''
                },
                comandas: [],
                formasPagamento: [],
                breadcrumb: [
                    {name: 'Relatórios', to: null},
                    {name: 'Faturamento por Forma de Pagamento', to: null}
                ],
            }
        },
        watch: {
            'filtro.agrupamento': function (val) {
                if (val === 'mes') {
                    this.filtro.data_inicio = moment(this.filtro.data_inicio).startOf('year').format('YYYY-MM-DD');
                    this.filtro.data_fim = moment().format('YYYY-MM-DD');
                } else if (val === 'dia') {
                    this.filtro.data_inicio = moment().startOf('month').format('YYYY-MM-DD');
                    this.filtro.data_fim = moment().format('YYYY-MM-DD');
                }

                this.buscarDados();
            },
            'filtro.data_inicio': function () {
                this.buscarDados();
            },
            'filtro.data_fim': function () {
                this.buscarDados();
            },
        },
        computed: {
            series() {
                const newSeries = [];
                this.formasPagamento.forEach(forma => {
                    const dataSeries = this.dataSeries(forma);
                    newSeries.push({
                        name: forma.nome,
                        data: dataSeries,
                        type: 'bar',
                        label: {
                            show: true,
                            position: 'top',
                            formatter: (params) => {
                                if (params.value) {
                                    return this.$options.filters.money(params.value)
                                }
                                return '';
                            }
                        },
                    });
                });

                return newSeries;
            },

            datas() {
                const datas = [];
                let dataInicio = moment(this.filtro.data_inicio);
                const dataFim = moment(this.filtro.data_fim);

                if (this.filtro.agrupamento === 'dia') {
                    const diferenca = dataFim.diff(dataInicio, 'days');
                    datas.push(dataInicio.format('DD/MM/YYYY'));
                    for (let i = 1; i <= diferenca; i++) {
                        dataInicio = moment(this.filtro.data_inicio);
                        datas.push(dataInicio.add(i, 'days').format('DD/MM/YYYY'));
                    }
                } else if (this.filtro.agrupamento === 'mes') {
                    const diferenca = dataFim.endOf('month').diff(dataInicio.startOf('month'), 'months');
                    for (let i = 0; i <= diferenca; i++) {
                        dataInicio = moment(this.filtro.data_inicio);
                        datas.push(dataInicio.add(i, 'months').format('MMM YYYY'));
                    }
                }
                return datas
            },

            option() {
                return {
                    xAxis: {
                        type: 'category',
                        data: this.datas,
                        axisLabel: {
                            rotate: 50
                        },
                        splitLine: {
                            lineStyle: {
                                type: 'dashed'
                            }
                        },
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        },
                        formatter: (params) => {
                            let legend = `${params[0].axisValueLabel} <br>`;
                            params.forEach(param => {
                                legend += `${param.marker} ${param.seriesName}: `
                                    + this.$options.filters.money(param.value) + ' <br>';
                            });
                            return legend;
                        }
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: {
                            formatter: (value) => {
                                return this.$options.filters.money(value)
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                type: 'dashed'
                            }
                        },
                    },
                    series: this.series
                }
            }
        },
        methods: {
            ...mapActions(["buscarComandas", "buscarMeiosPagamentos"]),

            dataSeries(formaPagamento) {
                const dataSeries = [];
                if (this.filtro.agrupamento === 'dia') {
                    this.datas.forEach(data => {
                        const comandas = this.comandas
                            .filter(comanda => moment(comanda.data).format('DD/MM/YYYY') === data);

                        let total = 0;
                        comandas.forEach(comanda => {
                            comanda.comanda_forma_pagamentos.forEach(pagamento => {
                                if (pagamento.meios_pagamento_id === formaPagamento.id) {
                                    total += pagamento.valor;
                                }
                            });
                        });

                        dataSeries.push(total);
                    });
                } else if (this.filtro.agrupamento === 'mes') {
                    let dataInicio = moment(this.filtro.data_inicio);
                    const dataFim = moment(this.filtro.data_fim);
                    const diferenca = dataFim.endOf('month').diff(dataInicio.startOf('month'), 'months');
                    for (let i = 0; i <= diferenca; i++) {
                        dataInicio = moment(this.filtro.data_inicio);
                        const mesInicio = !i ? dataInicio.add(i, 'months').format('YYYY-MM-DD') :
                            dataInicio.add(i, 'months').startOf('month').format('YYYY-MM-DD');

                        dataInicio = moment(this.filtro.data_inicio);
                        const mesFim = dataInicio.add(i, 'months').endOf('month').format('YYYY-MM-DD');
                        const comandas = this.comandas.filter(comanda => moment(comanda.data).isBetween(mesInicio, mesFim));

                        let total = 0;
                        comandas.forEach(comanda => {
                            comanda.comanda_forma_pagamentos.forEach(pagamento => {
                                if (pagamento.meios_pagamento_id === formaPagamento.id) {
                                    total += pagamento.valor;
                                }
                            });
                        });

                        dataSeries.push(total)
                    }
                }

                return dataSeries;
            },

            buscarDados() {
                this.load = true;
                let filtro = 'with[]=comandaFormaPagamentos';
                filtro += '&with[]=comandaItens';
                filtro += `&where[tipo]=E`;
                filtro += `&where[estornada]=0`;
                filtro += `&where[status]=F`;
                filtro += `&whereBetween[data]=${this.filtro.data_inicio} 00:00:00,${this.filtro.data_fim} 23:59:59`;

                this.buscarComandas(filtro).then(response => {
                    this.comandas = response.data;
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>
