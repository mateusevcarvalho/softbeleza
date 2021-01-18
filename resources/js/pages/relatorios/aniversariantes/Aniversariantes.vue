<template>
    <page-component title="Lista de Aniversariantes" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="card">
                <form class="card-body" @submit.prevent="buscarDados">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>Mês de aniversário:</label>
                            <select class="form-control" v-model="mes">
                                <option v-for="mesAtual in dataMeses" :value="mesAtual.value">{{mesAtual.name}}</option>
                            </select>
                        </div>
                    </div>
                    <hr>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Pesquisar</button>
                </form>
            </div>

            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data Nascimento</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Ticket Médio</th>
                    <th>Última Visita</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="itemCliente in clientes.data">
                    <td>{{itemCliente.individuo.nome}}</td>
                    <td>{{itemCliente.individuo.data_nascimento | dateBr}}</td>
                    <td>{{itemCliente.individuo.email_contato ? itemCliente.individuo.email_contato : '-'}}</td>
                    <td>{{itemCliente.individuo.celular | celular}}</td>
                    <td>
                    <span v-if="itemCliente.comandas.length">
                        {{ticketMedio(itemCliente.comandas) | money}}
                    </span>
                        <span v-else>-</span>
                    </td>
                    <td>
                    <span v-if="itemCliente.comandas.length">
                        {{itemCliente.comandas[0].data | dateTimeBr}}
                    </span>
                        <span v-else>-</span>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="mt-3">
                <b-pagination
                        v-model="page"
                        :total-rows="clientes.total"
                        :per-page="clientes.per_page"
                        @input="buscarDados"
                ></b-pagination>
            </div>
        </card-component>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'
    import meses from './../../../datas/meses'
    import moment from 'moment'

    export default {
        name: "Aniversariantes",
        created() {
            this.dataMeses = meses;
            this.mes = moment().month();
            this.buscarDados();
        },
        data() {
            return {
                load: false,
                clientes: [],
                dataMeses: [],
                mes: 1,
                page: 1,
                breadcrumb: [
                    {name: 'Relatórios', to: null},
                    {name: 'Lista de Aniversariantes', to: null}
                ],
            }
        },

        methods: {
            ...mapActions(["buscarClientes"]),

            ticketMedio(comandas) {
                const total = comandas.reduce((a, b) => a + b.valor_pago, 0);
                const quantidadeComandas = comandas.length;
                return total / quantidadeComandas;
            },


            buscarDados(page = 1) {
                let filtro = 'with[]=individuo&';
                filtro += 'with[]=comandas&';
                filtro += `whereMonthHas[individuo]=data_nascimento,${this.mes + 1}`;

                this.load = true;
                this.buscarClientes(filtro + `&page=${page}`).then(response => {
                    this.clientes = response.data;
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
