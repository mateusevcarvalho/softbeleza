import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarProfissionais({commit}, search = '') {
            return axios.get('/api/profissionais?' + search);
        },

        buscarHorariosProfissional({}, profissionalId) {
            return axios.get('/api/profissionais/horarios-trabalho?where[profissional_id]=' + profissionalId);
        }
    }
}
