export default {
    state: {
        username: ''
    },

    mutations: {
        ADD_USERNAME(state, name) {
            state.username = name
        }
    },

    getters: {
        username: (state) => {
            return state.username
        }
    },

    actions: {
        addUsername({commit}, username) {
            commit('ADD_USERNAME', username)
        }
    }
}
