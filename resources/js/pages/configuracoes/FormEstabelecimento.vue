<template>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col-sm-4 form-group">
                <label>Nome Fantasia <b class="text-danger">*</b></label>
                <input type="text" class="form-control" v-model="form.individuo.nome" required>
            </div>

            <div class="col-sm-4 form-group">
                <label>Razão Social</label>
                <input type="text" class="form-control" v-model="form.individuo.razao_social">
            </div>

            <div class="col-sm-4 form-group">
                <label>CNPJ</label>
                <input type="text" class="form-control" v-model="form.individuo.documento"
                       v-mask="'##.###.###/####-##'">
            </div>

            <div class="col-sm-4 form-group">
                <label>E-mail</label>
                <input type="text" class="form-control" v-model="form.individuo.email_contato">
            </div>

            <div class="col-sm-3 form-group">
                <label>Celular</label>
                <input type="text" class="form-control" v-model="form.individuo.celular"
                       v-mask="'(##) #####-####'">
            </div>

            <div class="col-sm-3 form-group">
                <label>Telefone</label>
                <input type="text" class="form-control" v-mask="'(##) ####-####'"
                       v-model="form.individuo.telefone_fixo">
            </div>

            <div class="col-sm-2 form-group">
                <label>Tipo<b class="text-danger">*</b></label>
                <select class="form-control" v-model="form.tipos_estabelecimento_id">
                    <option :value="tipo.id" v-for="tipo in tipos">
                        {{tipo.nome}}
                    </option>
                </select>
            </div>

            <div class="col-sm-4 form-group">
                <label>Responsável</label>
                <input type="text" class="form-control" v-model="form.individuo.responsavel">
            </div>

            <div class="col-sm-3 form-group">
                <label>Contato Responsável</label>
                <input type="text" class="form-control" v-model="form.individuo.contato_responsavel"
                       v-mask="['(##) ####-####', '(##) #####-####']">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "FormEstabelecimento",
        props: {
            formEstabelecimento: {
                required: true
            },
            tipos: {
                required: true
            }
        },
        data() {
            return {
                load: true,
                form: {...this.formEstabelecimento}
            }
        },
        methods: {
            onSubmit() {
                this.$emit('onLoad', true);
                axios.put('/api/estabelecimentos/' + this.form.id, this.form)
                    .then(_ => {
                        this.$snotify.success('Estabelecimento salvo.', 'Sucesso');
                        this.$emit('onLoad', false);
                    }).catch(error => {
                    this.$store.dispatch('handleCatchError', error);
                    this.$emit('onLoad', false);
                })
            }
        },
        watch: {
            formEstabelecimento(newValue) {
                this.form = newValue;
            }
        }
    }
</script>

<style scoped>

</style>
