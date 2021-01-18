<template>
    <page-component title="Faturamento por Período" :breadcrumb="breadcrumb">
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
    import {mapActions} from 'vuex'
    import moment from 'moment'

    export default {
        name: "FaturamentoPeriodo",
        created() {
            this.filtro.data_inicio = moment().startOf('month').format('YYYY-MM-DD');
            this.filtro.data_fim = moment().format('YYYY-MM-DD');
            this.buscarDados();
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
                breadcrumb: [
                    {name: 'Relatórios', to: null},
                    {name: 'Faturamento por Período', to: null}
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

            series() {
                const newData = {liquido: [], taxas: [], rateio: [], total: []};
                if (this.filtro.agrupamento === 'dia') {
                    this.datas.forEach(data => {
                        const comandas = this.comandas
                            .filter(comanda => moment(comanda.data).format('DD/MM/YYYY') === data);

                        let liquido = 0;
                        let taxas = 0;
                        let rateio = 0;
                        let total = 0;
                        comandas.forEach(comanda => {
                            comanda.comanda_itens.forEach(item => {
                                const valor = item.valor * item.quantidade;
                                let valorRateio = item.rateio ? (item.rateio * valor) / 100 : 0;
                                rateio += valorRateio;
                                liquido += valor - valorRateio;
                                total += valor;
                            });

                            comanda.comanda_forma_pagamentos.forEach(pagamento => {
                                const valorTaxa = pagamento.taxa ? (pagamento.valor * pagamento.taxa) / 100 : 0;
                                taxas += valorTaxa;
                                liquido -= valorTaxa;
                            });
                        });

                        newData.total.push(total);
                        newData.rateio.push(rateio);
                        newData.liquido.push(liquido);
                        newData.taxas.push(taxas);
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
                        let liquido = 0;
                        let taxas = 0;
                        let rateio = 0;
                        let total = 0;

                        comandas.forEach(comanda => {
                            comanda.comanda_itens.forEach(item => {
                                const valor = item.valor * item.quantidade;
                                let valorRateio = item.rateio ? (item.rateio * valor) / 100 : 0;
                                rateio += valorRateio;
                                liquido += valor - valorRateio;
                                total += valor;
                            });

                            comanda.comanda_forma_pagamentos.forEach(pagamento => {
                                const valorTaxa = pagamento.taxa ? (pagamento.valor * pagamento.taxa) / 100 : 0;
                                taxas += valorTaxa;
                                liquido -= valorTaxa;
                            });
                        });

                        newData.total.push(total);
                        newData.rateio.push(rateio);
                        newData.liquido.push(liquido);
                        newData.taxas.push(taxas);
                    }
                }
                return newData;
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
                    legend: {
                        data: ['Taxas', 'Rateio', 'Líquido', 'Total']
                    },
                    series: [{
                        name: 'Taxas',
                        data: this.series.taxas,
                        type: 'bar',
                        stack: 'Mon',
                        color: ['#e07c79'],
                        label: {
                            show: true,
                            position: 'inside',
                            formatter: (params) => {
                                if (params.value) {
                                    return this.$options.filters.money(params.value)
                                }
                                return '';
                            }
                        },
                    }, {
                        name: 'Rateio',
                        data: this.series.rateio,
                        type: 'bar',
                        stack: 'Mon',
                        color: ['#3663db'],
                        label: {
                            show: true,
                            position: 'inside',
                            formatter: (params) => {
                                if (params.value) {
                                    return this.$options.filters.money(params.value)
                                }
                                return '';
                            }
                        },
                    }, {
                        name: 'Líquido',
                        data: this.series.liquido,
                        type: 'bar',
                        stack: 'Mon',
                        color: ['#2ce044'],
                        label: {
                            show: true,
                            position: 'inside',
                            formatter: (params) => {
                                if (params.value) {
                                    return this.$options.filters.money(params.value)
                                }
                                return '';
                            }
                        },
                    }, {
                        name: 'Total',
                        data: this.series.total,
                        type: 'line',
                        smooth: true,
                        color: ['#434343'],
                        label: {
                            show: true,
                            position: 'top',
                            formatter: (params) => {
                                return this.$options.filters.money(params.value)
                            }
                        },
                    }]
                }
            }
        },
        methods: {
            ...mapActions(["buscarComandas"]),

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

<style>
    .echarts {
        width: 100%;
        height: 100%;
    }
</style>
