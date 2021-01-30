<template>
    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="/images/softbeleza-logo-azul.png" alt="Logo SoftBeleza">
            <h2>Pagamento Mensalidade SoftBeleza</h2>
            <p class="lead text-muted mt-3">
                Cadastre e realize abaixo a forma de pagamento de suas mensalidades. O lançamento da cobrança será
                realizado todo dia {{ diaHoje }} em sua fatura <b class="text-dark">automaticamente</b>.
            </p>
        </div>

        <div class="row">
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Seu carrinho</span>
                    <span class="badge badge-secondary badge-pill">1</span>
                </h4>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">Plano Mensal Softbeleza</h6>
                            <small class="text-muted">Assinatura plano mensal SoftBeleza</small>
                        </div>
                        <span class="text-muted">R$49,90</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total (BRL)</span>
                        <strong>R$49,90</strong>
                    </li>
                </ul>

            </div>
            <div class="col-md-8 order-md-1">
                <h4 class="mb-3">Pagamento</h4>
                <hr>
                <form @submit.prevent="onSubmit">
                    <div class="d-block my-3">
                        <b-form-group label="Forma de Pagamento" v-slot="{ ariaDescribedby }">
                            <b-form-radio-group
                                v-model="form.tipo_pagamento"
                                :options="radioOptions"
                                :aria-describedby="ariaDescribedby"
                                name="some-radios"
                                value="C"/>
                        </b-form-group>
                    </div>
                    <div class="row" v-if="form.tipo_pagamento === 'C'">
                        <div class="col-md-12 mb-3">
                            <label for="cc-nome">Nome no cartão</label>
                            <input type="text" class="form-control" id="cc-nome" required v-model="form.nome_cc"
                                   v-uppercase>
                            <small class="text-muted">Nome completo, como mostrado no cartão.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cc-numero">Número do cartão de crédito</label>
                            <input type="text" class="form-control" id="cc-numero" placeholder="____ ____ ____ ____"
                                   required v-mask="'#### #### #### ####'" v-model="form.numero_cc">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="cc-expiracao">Expiração</label>
                            <input type="text" class="form-control" id="cc-expiracao" placeholder="__/____" required
                                   v-mask="'##/####'" v-model="form.expiracao_cc">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cc-cvv">CVV</label>
                            <input type="text" class="form-control" id="cc-cvv" placeholder="___" required
                                   v-mask="'###'" v-model="form.cvv_cc">
                        </div>
                    </div>
                    <hr class="mb-4">
                    <button class="btn btn-success btn-lg btn-block" type="submit" v-if="form.tipo_pagamento === 'C'"
                            :disabled="load">
                        <i class="fas fa-credit-card"></i> REALIZAR PAGAMENTO
                    </button>
                    <button class="btn btn-success btn-lg btn-block" type="submit" v-else :disabled="load">
                        <i class="fas fa-barcode"></i> GERAR BOLETO
                    </button>
                </form>

                <div class="float-right mt-3">
                    <img class="d-block mx-auto mb-4" src="/images/selo.png" alt="Selo" width="100">
                </div>
            </div>
        </div>

        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; {{ anoHoje }} SoftBeleza</p>
            <!--            <ul class="list-inline">-->
            <!--                <li class="list-inline-item"><a href="#">Privacidade</a></li>-->
            <!--                <li class="list-inline-item"><a href="#">Termos</a></li>-->
            <!--                <li class="list-inline-item"><a href="#">Suporte</a></li>-->
            <!--            </ul>-->
        </footer>
    </div>

</template>

<script>
import moment from "moment";
import axios from "axios";

const formInitial = {
    uuid: '',
    tipo_pagamento: 'C',
    nome_cc: '',
    numero_cc: '',
    expiracao_cc: '',
    cvv_cc: '',
}

export default {
    name: "Checkout",
    created() {
        this.form.uuid = this.$route.params.uuid;
    },
    data() {
        return {
            diaHoje: parseInt(moment().format('DD')) >= 30 ? 30 : parseInt(moment().format('DD')),
            anoHoje: moment().format('YYYY'),
            load: false,
            radioOptions: [
                {text: 'Cartão de Crédito', value: 'C'},
                {text: 'Boleto', value: 'B'},
            ],
            form: formInitial
        }
    },
    methods: {
        async onSubmit() {
            try {
                this.load = true;
                const {data} = await axios.post('/api/checkout', this.form);
                if (data?.success) {
                    if (this.form.tipo_pagamento === 'C') {
                        this.$swal.fire({
                            allowOutsideClick: false,
                            icon: 'success',
                            title: 'Sucesso',
                            html: data.msg
                        }).then((_) => {
                            this.form = {
                                uuid: this.$route.params.uuid,
                                tipo_pagamento: 'C',
                                nome_cc: '',
                                numero_cc: '',
                                expiracao_cc: '',
                                cvv_cc: '',
                            }
                            window.location.href = Url + '/';
                        });
                    } else {
                        window.location.href = data.data.url_boleto;
                    }
                } else {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Falha no Pagamento',
                        html: data.msg
                    });
                }
                this.load = false;
            } catch (e) {
                this.load = false;
                await this.$store.dispatch('handleCatchError', e);
            }
        }
    }
}
</script>

<style scoped>

</style>
