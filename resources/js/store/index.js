import { createStore } from 'vuex'
import { token } from './modules/token'
import { posts } from './modules/posts'
import { user } from './modules/user'
import { loading } from './modules/loading'


const store = createStore({
    modules: {
        token,
        posts,
        user,
        loading
    }
})

export default store