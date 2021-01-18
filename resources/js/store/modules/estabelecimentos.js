import axios from 'axios'

export default {
    state: {},

    mutations: {},

    actions: {
        buscarEstabelecimento({}, search = '') {
            return axios.get('/api/estabelecimentos?' + search);
        },

        buscarTiposEstabelecimento() {
            return axios.get('/api/tipos-estabelecimentos');
        },

        buscarHorariosEstabelecimentos({}) {
            return  axios.get('/api/estabelecimentos/horarios-funcionamento')
        }
    }
}
