import { createStore } from 'vuex'
import { token } from './modules/token'
import { posts } from './modules/posts'
import { user } from './modules/user'


const store = createStore({
    modules: {
        token,
        posts,
        user
    }
})

export default store