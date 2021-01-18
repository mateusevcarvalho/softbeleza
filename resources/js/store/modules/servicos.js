import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarServicos({commit}, search = '') {
            return axios.get('/api/servicos?' + search);
        },

        buscarServicoCategorias({commit}, search = '') {
            return axios.get('/api/servico-categorias?' + search);
        }
    }
}
