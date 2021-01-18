<template>
    <form>
        <div class="row">
            <div class="col">

                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Localize um produto ou serviço abaixo:</label>
                        <v-select :options="options"
                                  @search="onSearch"
                                  @input="onChangeSearch"
                                  placeholder="Digite o código ou nome"
                                  :reduce="option => option.id" label="nome"
                                  v-model="form.opcao_id">
                            <template #search="{attributes, events}">
                                <input class="vs__search" v-bind="attributes"
                                       v-on="events" style="height: calc(1.5em + 1rem + -4px) !important"/>
                            </template>
                            <template v-slot:option="option">
                                <span :class="option.icon"></span>
                                {{ option.nome }} <br>
                                <small>{{ option.tipo }}</small>
                            </template>
                            <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                        </v-select>
                    </div>

                    <div class="form-group col-sm-6">
                        <label>Profissionais</label>
                        <v-select :options="profissionaisServico"
                                  placeholder="-Selecione-"
                                  :disabled="form.tipo_opcao === 'produto' || !form.key"
                                  :reduce="profissional => profissional.id"
                                  :getOptionLabel="profissional => profissional.individuo.nome"
                                  @input="onProfissional"
                                  v-model="form.profissional_id">
                            <template #search="{attributes, events}">
                                <input class="vs__search" v-bind="attributes"
                                       v-on="events" style="height: calc(1.5em + 1rem + -4px) !important"/>
                            </template>
                            <div slot="no-options">Desculpe, não há opções correspondentes.</div>
                        </v-select>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label>Desconto</label>
                        <div class="d-flex">
                            <money class="form-control form-control-lg" v-model="form.valor_desconto"
                                   v-bind="desconto"></money>
                            <select class="form-control form-control-lg" v-model="form.tipo_desconto"
                                    style="width: 95px" @change="onInputDesconto">
                                <option value="D">R$</option>
                                <option value="P">%</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>Quantidade</label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-outline-secondary"
                                        @click.prevent="onQuantidade('diminuir')">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control" v-model="form.quantidade"
                                   @input="onInputQuantidade">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                        @click.prevent="onQuantidade('somar')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>Valor Unitário</label>
                        <money class="form-control form-control-lg" @input="onInputQuantidade"
                               v-model="form.valor"></money>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>Valor Total</label>
                        <money class="form-control form-control-lg" :disabled="true"
                               v-model="form.valor_total"></money>
                    </div>

                    <div class="col-sm-12">
                        <button type="button" class="btn btn-dark btn-lg btn-block"
                                @click.prevent="addItem">
                            <i class="fas fa-plus"></i> Incluir
                        </button>
                    </div>

                </div>

            </div>
            <div class="col">
                <div class="col">
                    <div class="callout callout-success" style="margin-top: -8px">
                        <h5><small><i class="fas fa-user-alt"></i></small> Cliente:</h5>
                        <a href="javascript:void(0)" @click.prevent="$bvModal.show('modal-clientes')">
                            <span v-if="!cliente">Selecionar Cliente</span>
                            <span v-else>
                            {{ cliente.individuo.nome }}
                        </span>
                        </a>

                        <span v-if="cliente" class="text-danger ml-3" @click.prevent="removeCliente"
                              style="cursor: pointer;"><i class="fas fa-trash"></i></span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-secondary">
                            <tr>
                                <th>Qtd</th>
                                <th>Item</th>
                                <th>Preço</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody v-if="formComanda.comanda_itens.length">
                            <tr v-for="(item, index) in formComanda.comanda_itens">
                                <td>{{ item.quantidade }}</td>
                                <td>{{ item.nomeItem }}</td>
                                <td>{{ item.valor_total | money }}</td>
                                <td class="text-center" style="cursor: pointer">
                                <span class="text-danger" @click.prevent="removeItem(index)">
                                    <i class="fas fa-trash"></i>
                                </span>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot v-if="formComanda.comanda_itens.length">
                            <tr class="table-secondary">
                                <td colspan="2"><b>Valor Total:</b></td>
                                <td><b>{{ valorTotalComanda | money }}</b></td>
                                <td></td>
                            </tr>
                            </tfoot>
                            <tfoot v-else>
                            <tr>
                                <td colspan="4" class="text-center">Nenhum item adicionado.</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <button type="button" class="btn btn-lg btn-block btn-info" @click.prevent="onAguardar">
                                <i class="fas fa-clock"></i> Aguardar
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-lg btn-block btn-danger" @click.prevent="onCancelar">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                        <div class="col-sm-12 mt-3">
                            <button type="button" class="btn btn-lg btn-block btn-success"
                                    @click.prevent="onFinalizarOperacao">
                                <i class="fas fa-sign-in-alt"></i> Finalizar Operação
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <div class="mb-3 row">
            <div class="col">
                <button type="button" class="btn btn-success" @click.prevent="$bvModal.show('modal-reforco')">
                    <i class="fas fa-plus-circle"></i> Reforço
                </button>
                <button type="button" class="btn btn-danger" @click.prevent="$bvModal.show('modal-saque')">
                    <i class="fas fa-minus-circle"></i> Saque
                </button>
                <button type="button" class="btn btn-dark"
                        @click.prevent="$bvModal.show('modal-comandas-abertas')">
                    <i class="fas fa-file-alt"></i>
                    Comandas Abertas ({{ caixa.comandas.length }})
                </button>
            </div>
            <div class="col text-right">
                <button type="button" class="btn btn-primary" @click.prevent="onFecharCaixa">
                    <i class="fas fa-cash-register"></i> Fechar caixa
                </button>
            </div>
        </div>

        <b-modal size="lg" id="modal-finalizar-operacao" title="Finalizar Operação" hide-footer>
            <finalizar-operacao @operacaoFinalizada="handleOperacaoFinalizada"
                                :meios-pagamentos="meiosPagamentos"
                                :comanda="formComanda"/>
        </b-modal>

        <b-modal size="lg" id="modal-comandas-abertas" title="Comandas Abertas" hide-footer>
            <comandas-abertas :comandas="caixa.comandas" @onComanda="handleOnComanda"/>
        </b-modal>

        <b-modal size="lg" id="modal-clientes" title="Selecione/Crie um cliente" hide-footer>
            <form-clientes @clienteId="handleClienteId" :clientes="clientes"/>
        </b-modal>

        <b-modal id="modal-saque" title="Sacar valores do caixa" hide-footer>
            <form-reforco-sangria :caixa="caixa" tipo="S"/>
        </b-modal>

        <b-modal id="modal-reforco" title="Reforçar caixa" hide-footer>
            <form-reforco-sangria :caixa="caixa" tipo="R"/>
        </b-modal>
    </form>
</template>

<script>
import lodash from "lodash";
import {mapActions} from 'vuex'
import axios from 'axios'
import FormClientes from "./FormClientes";
import moment from 'moment';
import ComandasAbertas from "./ComandasAbertas";
import FinalizarOperacao from "./FinalizarOperacao";
import FormReforcoSangria from "./FormReforcoSangria";

export default {
    name: "FormComanda",
    components: {FormReforcoSangria, FinalizarOperacao, ComandasAbertas, FormClientes},
    props: ['profissionais', 'caixa', 'meiosPagamentos', 'clientes'],
    data() {
        return {
            options: [],
            cliente: null,
            formComanda: {
                caixa_id: this.caixa.id,
                cliente_id: null,
                tipo: 'E',
                tipo_desconto_geral: '',
                valor_desconto_total: '',
                valor_total: this.valorTotalComanda,
                valor_pago: '',
                valor_pendente: '',
                data: '',
                status: '',
                comanda_itens: [],
            },
            form: {
                opcao_id: '',
                tipo_opcao: 'servico',
                profissional_id: '',
                unidade_medida: '',
                nomeItem: '',
                valor: 0,
                key: null,
                quantidade: 1,
                valor_desconto: 0,
                tipo_desconto: 'D',
                valor_total: 0,
                descontar_taxas_rateio: false,
                rateio: null,
            },
            desconto: {
                decimal: ',',
                thousands: '.',
                prefix: '',
                suffix: '',
                precision: 2,
                masked: false
            }
        }
    },
    computed: {
        formDefault() {
            return {
                opcao_id: '',
                tipo_opcao: 'servico',
                profissional_id: '',
                unidade_medida: '',
                nomeItem: '',
                valor: 0,
                key: null,
                quantidade: 1,
                valor_desconto: 0,
                tipo_desconto: 'D',
                valor_total: 0,
            }
        },

        valorTotalComanda() {
            return this.formComanda.comanda_itens.reduce((a, b) => a + b.valor_total, 0)
        },

        profissionaisServico() {
            const profissionaisServicos = []
            if (this.form.tipo_opcao === 'servico') {
                this.profissionais.forEach(profissional => {
                    if (profissional.servicos.find(servico => servico.id === this.form.key)) {
                        profissionaisServicos.push(profissional);
                    }
                });
            }

            return profissionaisServicos;
        }
    },
    watch: {
        'form.valor_desconto': function (_) {
            this.onInputDesconto();
        }
    },
    methods: {
        ...mapActions(["buscarServicos", "buscarProdutos"]),

        onProfissional() {
            if (this.form.tipo_opcao === 'servico') {
                const profissional = this.profissionais.find(profissional => profissional.id === this.form.profissional_id);
                const servicoProfissional = profissional.servicos.find(servico => servico.id === this.form.key);
                this.form.valor = servicoProfissional.pivot.valor;
                this.form.rateio = servicoProfissional.pivot.rateio;
                this.form.descontar_taxas_rateio = profissional.descontar_taxas_rateio;
            }
        },

        onFecharCaixa() {
            this.$snotify.confirm(`Tem certeza que deseja fechar o caixa ?`,
                'Confirmar', {
                    timeout: 5000,
                    showProgressBar: true,
                    closeOnClick: true,
                    pauseOnHover: true,
                    buttons: [
                        {text: 'Sim', action: (toast) => this.fecharCaixa(toast), bold: false},
                        {text: 'Não', action: (toast) => this.$snotify.remove(toast.id)},
                    ]
                });
        },

        fecharCaixa(toast) {
            this.$snotify.remove(toast.id);
            this.$emit('onLoad', true);
            const formData = {data_fechamento: moment().format('YYYY-MM-DD HH:mm:ss'), status: 'F'}
            axios.put('/api/caixas/' + this.caixa.id, formData).then(_ => {
                this.$snotify.success('Caixa encerrado.', 'Sucesso');
                this.$emit('loadCaixa');
            }).catch(error => {
                this.$store.dispatch('handleCatchError', error);
                this.$emit('onLoad', false);
            })
        },

        handleOperacaoFinalizada() {
            this.formComanda = {
                caixa_id: this.caixa.id,
                cliente_id: null,
                tipo: 'E',
                tipo_desconto_geral: '',
                valor_desconto_total: '',
                valor_total: 0,
                valor_pago: '',
                valor_pendente: '',
                data: '',
                status: '',
                comanda_itens: [],
            }
            this.cliente = null;
            this.$emit('loadCaixa');
        },

        handleOnComanda(comanda) {
            const comandaItens = comanda.comanda_itens;
            const newComandaItens = [];

            comandaItens.forEach(item => {
                newComandaItens.push({
                    produto_id: item.produto_id ? item.produto_id : null,
                    servico_id: item.servico_id ? item.servico_id : null,
                    nomeItem: item.produto_id ? item.produto.nome : item.servico.nome,
                    profissional_id: item.profissional_id,
                    valor: item.valor,
                    quantidade: item.quantidade,
                    unidade_medida: item.unidade_medida,
                    tipo_desconto: item.tipo_desconto,
                    valor_desconto: item.valor_desconto,
                    valor_total: item.valor_total,
                    rateio: item.rateio,
                    descontar_taxas_rateio: item.descontar_taxas_rateio,
                });
            });

            this.formComanda = {...comanda, comanda_itens: newComandaItens};
            this.cliente = comanda.cliente;
        },

        onFinalizarOperacao() {
            if (this.formComanda.comanda_itens.length) {
                this.formComanda.valor_total = this.valorTotalComanda;
                this.$bvModal.show('modal-finalizar-operacao')
            } else {
                this.$snotify.warning('Nenhum item foi adicionado.', 'Alerta')
            }
        },

        onCancelar() {
            if (this.formComanda.hasOwnProperty('id')) {
                this.$emit('onLoad', true);
                axios.delete('/api/comandas/' + this.formComanda.id).then(_ => {
                    this.$snotify.success('Operação cancelada.', 'Sucesso');
                    this.formComanda = {
                        caixa_id: this.caixa.id,
                        cliente_id: null,
                        tipo: 'E',
                        tipo_desconto_geral: '',
                        valor_desconto_total: '',
                        valor_total: 0,
                        valor_pago: '',
                        valor_pendente: '',
                        data: '',
                        status: '',
                        comanda_itens: [],
                    }
                    this.cliente = null;
                    this.$emit('loadCaixa');
                    this.$emit('onLoad', false);
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.$emit('onLoad', false);
                });
            } else {
                this.formComanda = {
                    caixa_id: this.caixa.id,
                    cliente_id: null,
                    tipo: 'E',
                    tipo_desconto_geral: '',
                    valor_desconto_total: '',
                    valor_total: 0,
                    valor_pago: '',
                    valor_pendente: '',
                    data: '',
                    status: '',
                    comanda_itens: [],
                }
                this.cliente = null;
                this.$emit('loadCaixa');
                this.$snotify.success('Operação cancelada.', 'Sucesso');
            }
        },

        onAguardar() {
            const formData = {
                ...this.formComanda,
                data: moment().format('YYYY-MM-DD HH:mm:ss'),
                status: 'A',
                valor_total: this.valorTotalComanda
            };

            if (!this.formComanda.comanda_itens.length) {
                this.$snotify.warning('Nenhum item foi adicionado.', 'Alerta')
            } else if (!this.formComanda.cliente_id) {
                this.$snotify.warning('Para aguardar, um clinte deve ser selecionado.', 'Alerta')
            } else if (this.formComanda.hasOwnProperty('id')) {
                this.$emit('onLoad', true);
                axios.put('/api/comandas/' + this.formComanda.id, formData).then(_ => {
                    this.$snotify.success('Comanda salva.', 'Sucesso');
                    this.formComanda = {
                        caixa_id: this.caixa.id,
                        cliente_id: null,
                        tipo: 'E',
                        tipo_desconto_geral: '',
                        valor_desconto_total: '',
                        valor_total: 0,
                        valor_pago: '',
                        valor_pendente: '',
                        data: '',
                        status: '',
                        comanda_itens: [],
                    }
                    this.cliente = null;
                    this.$emit('loadCaixa');
                    this.$emit('onLoad', false);
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.$emit('onLoad', false);
                });
            } else {
                this.$emit('onLoad', true);
                axios.post('/api/comandas', formData).then(_ => {
                    this.$snotify.success('Comanda salva.', 'Sucesso');
                    this.formComanda = {
                        caixa_id: this.caixa.id,
                        cliente_id: null,
                        tipo: 'E',
                        tipo_desconto_geral: '',
                        valor_desconto_total: '',
                        valor_total: 0,
                        valor_pago: '',
                        valor_pendente: '',
                        data: '',
                        status: '',
                        comanda_itens: [],
                    }
                    this.cliente = null;
                    this.$emit('loadCaixa');
                    this.$emit('onLoad', false);
                }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.$emit('onLoad', false);
                });
            }
        },

        removeCliente() {
            this.cliente = null;
            this.formComanda.cliente_id = null;
        },

        handleClienteId(cliente) {
            this.cliente = cliente;
            this.formComanda.cliente_id = cliente.id;
        },

        addItem() {
            if (this.form.tipo_opcao && (
                (this.form.tipo_opcao === 'servico' && this.form.profissional_id) ||
                (this.form.tipo_opcao === 'produto')
            )) {
                this.formComanda.comanda_itens.push({
                    produto_id: this.form.tipo_opcao === 'produto' ? this.form.key : '',
                    servico_id: this.form.tipo_opcao === 'servico' ? this.form.key : '',
                    nomeItem: this.form.nomeItem,
                    profissional_id: this.form.profissional_id,
                    valor: this.form.valor,
                    quantidade: this.form.quantidade,
                    unidade_medida: this.form.unidade_medida,
                    tipo_desconto: this.form.tipo_desconto,
                    valor_desconto: this.form.valor_desconto,
                    valor_total: this.form.valor_total,
                    rateio: this.form.rateio,
                    descontar_taxas_rateio: this.form.descontar_taxas_rateio
                });

                this.form = {...this.formDefault};
            } else {
                this.$snotify.warning('Informações incompletas no formulário.', 'Alerta')
            }
        },

        removeItem(index) {
            this.formComanda.comanda_itens.splice(index, 1);
        },

        onChangeSearch() {
            const option = this.options.find(option => option.id === this.form.opcao_id);
            this.form = {...this.formDefault, opcao_id: this.form.opcao_id};
            if (option) {
                this.form.valor = option.valor_unitario;
                this.form.valor_total = option.valor_unitario;
                this.form.tipo_opcao = option.tipo;
                this.form.key = option.key;
                this.form.unidade_medida = option.unidade_medida;
                this.form.nomeItem = option.nomeItem;
            }
        },

        onQuantidade(operacao) {
            if (operacao === 'diminuir') {
                this.form.quantidade -= 1;
                this.form.valor_total -= this.form.valor;
            } else {
                this.form.quantidade += 1;
                this.form.valor_total += this.form.valor;
            }

            if (this.form.valor_desconto) {
                this.onInputDesconto();
            }
        },

        onInputDesconto() {
            const valorTotal = this.form.quantidade * this.form.valor;
            if (this.form.tipo_desconto === 'D') {
                this.form.valor_total = valorTotal - this.form.valor_desconto;
            } else {
                const valorDesconto = (this.form.valor_desconto / 100) * valorTotal;
                this.form.valor_total = valorTotal - valorDesconto;
            }
        },


        onInputQuantidade() {
            this.form.valor_total = this.form.quantidade * this.form.valor;
            if (this.form.valor_desconto) {
                this.onInputDesconto();
            }
        },

        onSearch(search, loading) {
            if (search.trim() && search.trim().length >= 2) {
                loading(true);
                this.search(loading, search, this);
            }
        },

        search: lodash.debounce(function (loading, search, vm) {
            axios.all([
                vm.buscarServicos(`whereLike[nome]=${search}&orWhereLike[codigo]=${search}`),
                vm.buscarProdutos(`whereLike[nome]=${search}`),
            ]).then(axios.spread((responseServicos, responseProdutos) => {
                const servicos = responseServicos.data;
                const produtos = responseProdutos.data;
                const data = [];

                produtos.forEach(produto => {
                    data.push({
                        nome: produto.nome,
                        id: 'produto-' + produto.id,
                        tipo: 'produto',
                        key: produto.id,
                        valor_unitario: produto.valor,
                        unidade_medida: produto.unidade_medida_saida,
                        nomeItem: produto.nome,
                    })
                });

                servicos.forEach(servico => {
                    if (servico.status) {
                        data.push({
                            nome: servico.codigo + ' - ' + servico.nome,
                            id: 'servico-' + servico.id,
                            key: servico.id,
                            tipo: 'servico',
                            valor_unitario: servico.valor,
                            unidade_medida: null,
                            nomeItem: servico.nome,
                        });
                    }
                });

                vm.options = data;
                loading(false)
            })).catch(_ => {
                loading(false)
            });
        }, 350),
    }
}
</script>

<style scoped>

</style>
