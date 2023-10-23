import { createStore } from 'vuex'
import { token } from './modules/token'


const store = createStore({
    modules: {
        token: token
    }
})

export default store