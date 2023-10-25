import { createStore } from 'vuex'
import { token } from './modules/token'
import { posts } from './modules/posts'


const store = createStore({
    modules: {
        token,
        posts
    }
})

export default store