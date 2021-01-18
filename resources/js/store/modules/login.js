import axios from 'axios'
import Vue from 'vue'

export default {
    state: {
        usuario: {}
    },

    mutations: {
        ADD_USUARIO(state, usuario) {
            state.usuario = usuario
        }
    },

    getters: {},

    actions: {
        async login({commit}, {email, password}) {
            try {
                const {data} = await axios.post('/api/token', {email, password});
                const token = data.token;
                if (data.countHorarioAtendimentos && data.countMeiosPagamentos &&
                    data.countProfissionaisServicos && data.countServicos) {
                    localStorage.setItem('tour', '0');
                } else {
                    localStorage.setItem('tour', '1');
                }

                localStorage.setItem('_token', token);
                localStorage.setItem('dataUsuario', JSON.stringify(data.usuario));
                localStorage.setItem('nomeUsuario', data.nome);
                localStorage.setItem('estabelecimento', data.estabelecimento);
                window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
                commit('ADD_USUARIO', data);
                return data;
            } catch (e) {
                return false;
            }
        },

        async logout() {
            await axios.get('/api/logout');
            localStorage.clear();
            window.location.href = '/';
        },

        buscarUsuarios({commit}, search = '') {
            return axios.get('/api/usuarios?' + search);
        },

        buscarControleAcesso({commit}, search = '') {
            return axios.get('/api/controle-acesso?' + search);
        },

        handleCatchError({}, error) {
            if (error.hasOwnProperty('status') && error.status === 401) {
                localStorage.clear();
                Vue.$snotify.warning('Sessão expirada', 'Alerta');
                window.location.href = '/';
            } else if (error.hasOwnProperty('response') && error.response.status === 401) {
                localStorage.clear();
                Vue.$snotify.warning('Sessão expirada', 'Alerta');
                window.location.href = '/';
            } else if (error.hasOwnProperty('response') && error.response.status === 500) {
                Vue.$snotify.error('Falha interna, favor tente mais tarde.', 'Erro');
            } else if (error.hasOwnProperty('status') && error.status === 500) {
                Vue.$snotify.error('Falha interna, favor tente mais tarde.', 'Erro');
            } else if (error.hasOwnProperty('response') && error.response.status === 404) {
                Vue.$snotify.warning('Informação informada não encontada.', '404');
            } else if (error.hasOwnProperty('status') && error.status === 404) {
                Vue.$snotify.warning('Informação informada não encontada.', '404');
            }
        }
    }
}
