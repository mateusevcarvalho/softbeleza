import axios from 'axios';

export default {
    state: {},

    mutations: {},

    getters: {},

    actions: {
        buscarProdutos({commit}, search = '') {
            return axios.get('/api/produtos?' + search);
        },

        buscarTiposMovimentacoesEstoque() {
            return axios.get('/api/movimentacoes-estoque/tipos-movimentacoes')
        },
    }
}
