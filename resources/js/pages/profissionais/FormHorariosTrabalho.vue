<template>
    <form @submit.prevent="onSubmit">
        <div v-for="(dia, indexDia) in this.horarios.dias" :key="indexDia">
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
            <div class="col-sm-6">
                <button type="button" class="btn btn-dark" @click="$bvModal.hide('modal-horarios')">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
            <div class="col-sm-6 text-right">
                <button type="submit" class="btn btn-primary" :disabled="load">
                    Salvar
                    <span v-if="load">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </span>
                    <span v-else><i class="fas fa-save"></i></span>
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    import axios from "axios";

    export default {
        name: "FormHorariosTrabalho",
        props: {
            formDias: {
                required: true
            }
        },
        data() {
            return {
                load: false,
                horarios: {...this.formDias}
            }
        },

        methods: {
            async onSubmit() {
                try {
                    this.load = true;
                    await axios.post('/api/profissionais/horarios-trabalho', this.horarios);
                    this.$snotify.success('Horários definidos', 'Sucesso');
                    this.$emit('loadData');
                    this.$bvModal.hide('modal-horarios');
                    this.load = false;
                } catch (error) {
                    this.$store.dispatch('handleCatchError', error);
                    this.$snotify.error('Não foi possível salvar, falha interna.', 'Erro');
                    this.load = false;
                }
            },

            addHorarios(index) {
                this.horarios.dias[index].horarios.push({hora_inicio: null, hora_fim: null});
            },

            deleteHorario(indexDia, indexHorario) {
                this.horarios.dias[indexDia].horarios.splice(indexHorario, 1);
            },
        }
    }
</script>

<style scoped>

</style>
