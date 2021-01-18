import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarClientes({commit}, search = '') {
            return axios.get('/api/clientes?' + search);
        },
    }
}
