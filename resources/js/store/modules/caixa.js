import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarCaixas({commit}, search = '') {
            return axios.get('/api/caixas?' + search);
        },

        buscarComandas({commit}, search = '') {
            return axios.get('/api/comandas?' + search);
        }
    }
}
