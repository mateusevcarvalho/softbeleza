import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarMeiosPagamentos({commit}, search = '') {
            return axios.get('/api/meios-pagamentos?' + search);
        },
    }
}
