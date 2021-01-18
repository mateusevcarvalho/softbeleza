import axios from 'axios'

export default {

    actions: {
        buscarLocais() {
            return axios.get('/api/locais');
        }
    }
}
