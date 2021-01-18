import axios from 'axios'

export default {

    actions: {
        async buscarTenants({state}, search) {
            try {
                console.log(search);
                const response = await axios.get('/api/tenants?' + search);
                return response
            } catch (e) {
                console.log(e);
            }
        }
    }
}
