import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarFornecedores({commit}, search = '') {
            return axios.get('/api/fornecedores?' + search);
        },
    }
}
