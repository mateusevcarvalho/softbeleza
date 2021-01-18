import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

import perfil from './modules/perfil'
import locais from './modules/locais'
import tenants from './modules/tenants'
import login from './modules/login'
import estabelecimentos from './modules/estabelecimentos'
import enderecos from './modules/enderecos'
import servicos from './modules/servicos'
import profissionais from './modules/profissionais'
import meiosPagamentos from './modules/meiosPagamentos'
import clientes from './modules/clientes'
import estoque from './modules/estoque'
import fornecedores from './modules/fornecedores'
import agendamentos from './modules/agendamentos'
import caixa from './modules/caixa'

const store = new Vuex.Store({
    strict: true,
    modules: {
        perfil,
        locais,
        tenants,
        login,
        estabelecimentos,
        enderecos,
        servicos,
        profissionais,
        meiosPagamentos,
        clientes,
        estoque,
        fornecedores,
        agendamentos,
        caixa,
    }
});

export default store;

