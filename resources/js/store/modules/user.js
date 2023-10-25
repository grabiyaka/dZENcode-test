export const user = {
    state: () => ({
        user: {},
    }),
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
    },
    getters: {
        getUser(state) {
            return state.user
        }
    },
    actions: {
        fetchUser({ commit }) {
            axios.get('/api/user')
                .then(response => {
                    const user = response.data;
                    commit('setUser', user);
                    console.log(response.data);
                })
                .catch(error => {
                    commit('setUser', {});
                });
        },
    },
}