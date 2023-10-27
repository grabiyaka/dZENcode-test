export const loading = {
    state: () => ({
        loading: false,
    }),
    mutations: {
        setLoading(state, loading){
            state.loading = loading;
        },
    },
    getters: {
        getLoading(state){
            return state.loading
        }
    }
}