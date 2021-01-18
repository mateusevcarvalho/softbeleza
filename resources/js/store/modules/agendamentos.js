import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarAgendamentos({commit}, search = '') {
            return axios.get('/api/agendamentos?' + search);
        },
    }
}
