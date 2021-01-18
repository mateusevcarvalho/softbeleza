<template>
    <form @submit.prevent="onSubmit">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Defina os horários em que seu estabelecimento fica aberto
            para que seus clientes podem marcar horário. Você também pode adicionar os intervalos
            de trabalho clicando no botão de adicionar em cada dia.
        </div>

        <div v-for="(dia, indexDia) in dias" :key="indexDia">
            <div class="row">
                <div class="col-md-1">
                    <h6>{{dia.name}}:</h6>
                </div>
                <div v-for="(horario, index) in dia.horarios" :key="index" class="col-md-4 mb-3">
                    <ul class="list-group ">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="mr-2 text-danger"
                                 v-b-tooltip.hover title="Remover Turno"
                                 style="cursor: pointer"
                                 @click.prevent="deleteHorario(indexDia, index)">
                                <i class="fas fa-trash"></i>
                            </div>
                            <span>Das:</span>
                            <div class="ml-1 mr-1">
                                <input v-model="horario.hora_inicio" type="time"
                                       class="form-control" required/>
                            </div>
                            <span>Até:</span>
                            <div class="ml-1">
                                <input v-model="horario.hora_fim" type="time" class="form-control"
                                       required/>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="col-md-1 mb-3">
                    <button
                        v-b-tooltip.hover title="Adicionar Turno"
                        type="button"
                        class="btn btn-sm btn-primary"
                        @click.prevent="addHorarios(indexDia)"
                    >
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
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
        name: "FormHorarioFuncionamento",
        props: {
            formDias: {
                required: true
            }
        },
        data() {
            return {
                dias: {...this.formDias}
            }
        },

        methods: {
            async onSubmit() {
                try {
                    this.$emit('onLoad', true);
                    await axios.post('/api/estabelecimentos/horarios-funcionamento', this.dias);
                    this.$snotify.success('Horários definidos', 'Sucesso');
                    this.$emit('onLoad', false);
                } catch (error) {
                    this.$store.dispatch('handleCatchError', error);
                    this.$snotify.error('Não foi possível salvar, falha interna.', 'Erro');
                    this.$emit('onLoad', false);
                }
            },

            addHorarios(index) {
                this.dias[index].horarios.push({hora_inicio: null, hora_fim: null});
            },

            deleteHorario(indexDia, indexHorario) {
                this.dias[indexDia].horarios.splice(indexHorario, 1);
            },
        }
    }
</script>

<style scoped>

</style>
