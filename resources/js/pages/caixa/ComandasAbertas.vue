<template>
    <div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Valor Total</th>
                <th>Data</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="comanda in comandas" style="cursor: pointer" @click="onComanda(comanda)">
                <td>{{comanda.cliente.individuo.nome | firstLastName}}</td>
                <td>{{comanda.cliente.individuo.celular | celular}}</td>
                <td>{{comanda.valor_total | money}}</td>
                <td>{{comanda.data | dateTimeBr}}</td>
            </tr>
            </tbody>
        </table>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-comandas-abertas')">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ComandasAbertas",
        props: ['comandas'],
        methods: {
            onComanda(comanda) {
                this.$bvModal.hide('modal-comandas-abertas');
                this.$emit('onComanda', comanda);
            }
        }
    }
</script>

<style scoped>

</style>
