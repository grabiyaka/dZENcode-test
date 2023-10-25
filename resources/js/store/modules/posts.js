export const posts = {
    state: () => ({
        posts: [],
    }),
    mutations: {
        setPosts(state, posts){
            state.posts = posts;
        },
    },
    getters: {
        getPosts(state){
            return state.posts
        }
    },
}