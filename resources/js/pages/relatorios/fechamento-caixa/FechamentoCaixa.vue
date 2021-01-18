<template>
    <page-component title="Fechamento de Caixa" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <form class="card" @submit.prevent="handleLoadData(1)">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 form-group">
                            <label>Abertura a partir de</label>
                            <input type="date" class="form-control" v-model="filter.data_inicio" required>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Fechamento até</label>
                            <input type="date" class="form-control" v-model="filter.data_fim">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Status</label>
                            <select class="form-control" v-model="filter.status">
                                <option value="">Todos</option>
                                <option value="A">Aberto</option>
                                <option value="F">Fechado</option>
                            </select>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Usuários</label>
                            <v-select :options="usuarios"
                                      placeholder="Todos"
                                      :reduce="usuario => usuario.id"
                                      :getOptionLabel="usuario => usuario.individuo.nome"
                                      v-model="filter.usuario_id">
                                <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                            </v-select>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Pesquisar</button>
                </div>
            </form>

            <div class="table-responsive mt-3">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Status</th>
                        <th>Abertura</th>
                        <th>Fechamento</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="caixa in caixas.data">
                        <td>{{caixa.usuario.individuo.nome}}</td>
                        <td>
                            <span v-if="caixa.status === 'A'"><b class="text-danger">Aberto</b></span>
                            <span v-else>Fechado</span>
                        </td>
                        <td>{{caixa.data_abertura | dateTimeBr}}</td>
                        <td>
                            <span v-if="caixa.data_fechamento">{{caixa.data_fechamento | dateTimeBr}}</span>
                            <span v-else>-</span>
                        </td>
                        <td width="120" class="text-center">
                            <router-link tag="span" :to="'/relatorios/fechamento-caixa/detalhes/' + caixa.id"
                                         class="text-bold text-info mr-2" style="cursor: pointer">
                                <i class="fas fa-file-alt"></i> Detalhes
                            </router-link>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="mt-3">
                    <b-pagination
                            v-model="page"
                            :total-rows="caixas.total"
                            :per-page="caixas.per_page"
                            @input="handleLoadData"
                    ></b-pagination>
                </div>
            </div>
        </card-component>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'
    import moment from 'moment'
    import axios from 'axios'

    export default {
        name: "FechamentoCaixa",
        created() {
            this.load = true;
            const search = this.montarPesquisa();
            axios.all([
                this.buscarCaixas('with[]=usuario.individuo&orderBy[data_abertura]=desc&page=1' + search),
                this.buscarUsuarios('with[]=individuo')
            ]).then(axios.spread((caixaResponse, usuariosResponse) => {
                this.caixas = caixaResponse.data;
                this.usuarios = usuariosResponse.data;
                this.load = false;
            })).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            });
        },
        data() {
            return {
                load: false,
                page: 1,
                filter: {
                    data_inicio: moment().subtract(1, 'month').format('YYYY-MM-DD'),
                    data_fim: '',
                    status: '',
                    usuario_id: '',
                },
                usuarios: [],
                caixas: {
                    total: 0,
                    per_page: 10,
                    data: []
                },
                breadcrumb: [
                    {name: 'Relatórios', to: null},
                    {name: 'Fechamento de Caixa', to: null}
                ],
            }
        },
        methods: {
            ...mapActions(["buscarCaixas", "buscarUsuarios"]),

            montarPesquisa() {
                let search = '';
                if (this.filter.data_inicio)
                    search += `&whereLg[data_abertura]=${this.filter.data_inicio} 00:00:00`;
                if (this.filter.data_fim)
                    search += `&whereSm[data_fechamento]=${this.filter.data_fim} 23:59:59`;
                if (this.filter.usuario_id)
                    search += `&where[usuario_id]=${this.filter.usuario_id}`;
                if (this.filter.status)
                    search += `&where[status]=${this.filter.status}`;

                return search;
            },

            handleLoadData(page = 1) {
                this.load = true;
                const search = this.montarPesquisa();
                this.buscarCaixas(`with[]=usuario.individuo&orderBy[data_abertura]=desc&page=${page}${search}`)
                    .then(response => {
                        this.caixas = response.data;
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
