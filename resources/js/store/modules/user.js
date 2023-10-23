export const user = {
    state: () => ({
        user: '',
    }),
    mutations: {
        setUser(state, user){
            state.user = user;
        },
    },
    getters: {
        getUser(state){
            return state.user
        }
    }
}