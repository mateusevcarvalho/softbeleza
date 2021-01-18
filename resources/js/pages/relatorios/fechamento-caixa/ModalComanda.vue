<template>
    <div>
        <div class="estorno" v-if="comanda.estornada">Cancelada</div>
        <h6 class="mb-3">
            <b><i class="fas fa-user"></i> Cliente:</b>
            <span v-if="comanda.cliente">{{comanda.cliente.individuo.nome}}</span>
            <span v-else>Não informado</span>
        </h6>

        <h6 class="mb-3">
            <b><i class="fas fa-tag"></i> Desconto Geral:</b>
            <span v-if="comanda.valor_desconto_total">
                <span v-if="comanda.tipo_desconto_geral === 'D'">{{comanda.valor_desconto_total | money}}</span>
                <span v-else>{{comanda.valor_desconto_total | percent}}</span>
            </span>
            <span v-else>{{0 | money}}</span>
        </h6>

        <h6 class="mt-4"><b><i class="icofont-coins"></i> Pagamentos</b></h6>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-secondary">
                <tr>
                    <th>Pagamento</th>
                    <th>Valor Pago</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="formaPagamento in comanda.comanda_forma_pagamentos">
                    <td>
                        {{formaPagamento.meios_pagamento.nome}}
                        <span v-if="formaPagamento.parcelas">({{formaPagamento.parcelas}})x</span>
                    </td>
                    <td>{{formaPagamento.valor | money}}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <hr>

        <h6><b><i class="fas fa-shopping-basket"></i> Itens</b></h6>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-secondary">
                <tr>
                    <th>Qtd</th>
                    <th>Item</th>
                    <th>Desconto</th>
                    <th>Preço</th>
                </tr>
                </thead>
                <tbody v-if="comanda.comanda_itens.length">
                <tr v-for="item in comanda.comanda_itens">
                    <td>{{item.quantidade}}</td>
                    <td>{{nomeProdutoServico(item)}}</td>
                    <td>
                        <span v-if="item.tipo_desconto === 'D'">
                            <span v-if="item.valor_desconto">{{item.valor_desconto | money}}</span>
                            <span v-else>-</span>
                        </span>
                        <span v-else>{{item.valor_desconto | percent}}</span>
                    </td>
                    <td>{{item.valor_total | money}}</td>
                </tr>
                </tbody>
                <tfoot v-if="comanda.comanda_itens.length">
                <tr class="table-secondary">
                    <td colspan="3"><b>Valor Total:</b></td>
                    <td><b>{{valorTotalComanda | money}}</b></td>
                </tr>
                </tfoot>
                <tfoot v-else>
                <tr>
                    <td colspan="4" class="text-center">Nenhum item foi adicionado.</td>
                </tr>
                </tfoot>
            </table>
        </div>

        <hr>
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-comanda')">
                    <i class="fas fa-times"></i> Fechar
                </button>
                <button type="button" class="btn btn-danger" @click="estornar" :disabled="!!comanda.estornada || load">
                    <b-spinner v-if="load" small />
                    <span v-else><i class="fas fa-sync-alt"></i></span>
                    Estornar
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "ModalComanda",
        props: ['comanda'],
        data() {
            return {
                load: false
            }
        },
        methods: {
            nomeProdutoServico(item) {
                if (item.produto) {
                    return item.produto.nome;
                } else {
                    return item.servico.nome;
                }
            },

            estornar() {
                this.load = true;
                axios.put('/api/estornar-comanda/' + this.comanda.id).then(_ => {
                    this.$snotify.success('Comanda estornada.', 'Sucesso');
                    this.load = false;
                    this.$emit('loadDados');
                    this.$bvModal.hide('modal-comanda');
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.$bvModal.hide('modal-comanda');
                    this.load = false;
                });
            }
        },
        computed: {
            valorTotalComanda() {
                return this.comanda.comanda_itens.reduce((a, b) => a + b.valor_total, 0);
            }
        }
    }
</script>

<style scoped>
    .estorno {
        border: 5px dashed #000;
        display: inline-block;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate3d(-50%, -50%, 0) rotate(-15deg);
        font-size: 60px;
        text-transform: uppercase;
        opacity: .15;
        padding: 10px 20px;
        z-index: 2;
    }
</style>
