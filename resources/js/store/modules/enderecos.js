import axios from 'axios';
import Vue from 'vue';

export default {
    actions: {
        buscaCep(_, cep) {
            return Vue.http.get(`https://viacep.com.br/ws/${cep}/json/`);
        },

        async buscaCidades(_, search = '') {
            return axios.get('/api/cidades?' + search);
        },

        buscaEstados(_, search = '') {
            return axios.get('/api/estados?' + search);
        }
    }
}
