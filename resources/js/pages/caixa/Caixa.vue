<template>
    <page-component title="Caixa" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <form-comanda :profissionais="profissionais"
                          :clientes="clientes"
                          :caixa="caixa" @onLoad="handleLoad"
                          :meios-pagamentos="meiosPagamentos"
                          v-if="caixa.hasOwnProperty('id')"
                          @loadCaixa="handleLoadCaixa"/>

            <form-abertura v-else @loadCaixa="handleLoadCaixa"/>
        </card-component>
    </page-component>
</template>

<script>
    import FormAbertura from "./FormAbertura";
    import axios from 'axios';
    import {mapActions} from 'vuex';
    import FormComanda from "./FormComanda";

    export default {
        name: "Caixa",
        components: {FormComanda, FormAbertura},
        created() {
            this.load = true;
            let withCaixas = 'with[]=comandas.comandaItens.produto';
            withCaixas += '&with[]=comandas.comandaItens.servico';
            withCaixas += '&with[]=comandas.cliente.individuo&';

            axios.all([
                this.buscarCaixas(withCaixas + 'where[status]=A&first=true&usuarioLogado=true'),
                this.buscarProfissionais('with[]=individuo&where[status]=A&with[]=servicos'),
                this.buscarMeiosPagamentos(),
                this.buscarClientes('with[]=individuo'),
            ]).then(axios.spread((resCaixa, resProfissionais, resMeiosPagamentos, resClientes) => {
                this.caixa = this.filtrarComandasAbertas(resCaixa.data);
                this.profissionais = resProfissionais.data;
                this.meiosPagamentos = resMeiosPagamentos.data;
                this.clientes = resClientes.data;
                this.load = false;
            })).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },
        data() {
            return {
                load: false,
                valor_abertura: 0,
                profissionais: [],
                meiosPagamentos: [],
                clientes: [],
                caixa: {
                    comandas: []
                },
                breadcrumb: [
                    {name: 'Caixa', to: null},
                ],
            }
        },
        methods: {
            ...mapActions(["buscarCaixas", "buscarProfissionais", "buscarMeiosPagamentos", "buscarClientes"]),

            handleLoad(event) {
                this.load = event;
            },

            filtrarComandasAbertas(caixa) {
                if (caixa.hasOwnProperty('comandas')) {
                    const newComandas = [];
                    caixa.comandas.forEach(comanda => {
                        if (comanda.status === 'A') {
                            newComandas.push(comanda);
                        }
                    });

                    const newCaixa = {...caixa, comandas: newComandas};
                    return newCaixa;
                } else {
                    return {comandas: []};
                }
            },

            handleLoadCaixa() {
                this.load = true;
                let withCaixas = 'with[]=comandas.comandaItens.produto';
                withCaixas += '&with[]=comandas.comandaItens.servico';
                withCaixas += '&with[]=comandas.cliente.individuo&';

                axios.all([
                    this.buscarCaixas(withCaixas + 'where[status]=A&first=true&usuarioLogado=true'),
                    this.buscarProfissionais('with[]=individuo&where[status]=A&with[]=servicos'),
                    this.buscarMeiosPagamentos(),
                ]).then(axios.spread((resCaixa, resProfissionais, resMeiosPagamentos) => {
                    this.caixa = this.filtrarComandasAbertas(resCaixa.data);
                    this.profissionais = resProfissionais.data;
                    this.meiosPagamentos = resMeiosPagamentos.data;
                    this.load = false;
                })).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },
        }
    }
</script>

<style scoped>

</style>
