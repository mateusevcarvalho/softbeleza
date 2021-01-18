<template>
    <page-component title="Meios de Pagamentos" :breadcrumb="breadcrumb">
        <card-component :load="load">
            <div class="row mb-3">
                <div class="col-sm-8">
                    <button type="button" class="btn btn-primary" @click.prevent="cadastrar">
                        <i class="fas fa-plus"></i> Cadastrar
                    </button>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Meio Pagamento</th>
                        <th>Tipo</th>
                        <th>Taxa</th>
                        <th>Repasse</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="meio in meiosPagamentos">
                        <td>{{meio.nome}}</td>
                        <td>{{meio.tipo_pagamento === 'V' ? 'Á vista' : 'Á prazo'}}</td>
                        <td>{{meio.taxa_operadora | percent}}</td>
                        <td>{{!meio.dias_repasse_operadora ? 'No mesmo dia' : meio.dias_repasse_operadora}}</td>
                        <td width="90" class="text-center">
                            <span class="text-bold text-info mr-2" style="cursor: pointer" @click="editar(meio)"
                                  v-b-tooltip.hover title="Editar">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="text-bold text-danger" style="cursor: pointer" @click="confirmar(meio)"
                                  v-b-tooltip.hover title="Apagar">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </card-component>

        <b-modal size="xs" id="modal-cadastro" title="Editar Meio Pagamento"
                 hide-footer>
            <form-meio-pagamento :meios-pagamento="meioPagamento" @loadData="handleLoadData"></form-meio-pagamento>
        </b-modal>
    </page-component>
</template>

<script>
    import {mapActions} from 'vuex'
    import FormMeioPagamento from "./FormMeioPagamento";
    import axios from "axios";

    export default {
        name: "MeiosPagamentos",
        components: {FormMeioPagamento},
        async created() {
            this.load = true;
            try {
                const {data} = await this.buscarMeiosPagamentos();
                this.meiosPagamentos = data;
                this.load = false;
            } catch (error) {
                this.$store.dispatch('handleCatchError', error);
                this.load = false;
            }
        },
        data() {
            return {
                load: false,
                page: 1,
                meiosPagamentos: [],
                meioPagamento: {
                    forma_pagamento: 'D',
                    estabelecimento_id: '',
                    nome: '',
                    tipo_pagamento: 'V',
                    max_parcelas: '',
                    taxa_operadora: 0,
                    dias_repasse_operadora: 0,
                },
                breadcrumb: [
                    {name: 'Configurações', to: null},
                    {name: 'Meios de Pagamentos', to: null},
                ],
            }
        },
        methods: {
            ...mapActions(['buscarMeiosPagamentos']),

            cadastrar() {
                this.meioPagamento = {
                    forma_pagamento: 'D',
                    estabelecimento_id: '',
                    nome: '',
                    tipo_pagamento: 'V',
                    max_parcelas: '',
                    taxa_operadora: 0,
                    dias_repasse_operadora: 0,
                };
                this.$bvModal.show('modal-cadastro')
            },

            editar(meio) {
                this.meioPagamento = {...meio};
                this.$bvModal.show('modal-cadastro')
            },

            confirmar(meio) {
                this.$snotify.confirm(`Tem certeza que deseja apagar o meio de pagamento: ${meio.nome} ?`,
                    'Confirmar', {
                        timeout: 5000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                        buttons: [
                            {text: 'Sim', action: (toast) => this.apagar(meio, toast), bold: false},
                            {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                        ]
                    });
            },

            apagar(meio, toast) {
                this.$snotify.remove(toast.id);
                this.load = true;
                axios.delete('/api/meios-pagamentos/' + meio.id).then(_ => {
                    this.$snotify.success('Meio pagamento apagado.', 'Sucesso');
                    this.handleLoadData();
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                });
            },

            handleLoadData() {
                this.load = true;
                this.buscarMeiosPagamentos().then(response => {
                    this.meiosPagamentos = response.data;
                    this.load = false;
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.load = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>
